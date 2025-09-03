<?php
/**
 * Arquivo: app/Http/Controllers/DeliveryAgreementController.php
 * Descrição: Controller para gerenciar acordos de entrega
 * Laravel Version: 12.x
 * Criado em: 03/01/2025
 */

namespace App\Http\Controllers;

use App\Models\DeliveryAgreement;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\SubOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeliveryAgreementController extends Controller
{
    /**
     * Criar nova proposta de entrega
     */
    public function store(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'sub_order_id' => 'required|exists:sub_orders,id',
            'type' => 'required|in:pickup,meet_location,custom_delivery,correios,transportadora',
            'description' => 'required|string|max:1000',
            'details' => 'nullable|array',
            'delivery_fee' => 'required|numeric|min:0',
            'estimated_date' => 'nullable|date|after:today',
            'estimated_time' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            $conversation = Conversation::findOrFail($request->conversation_id);
            
            // Verificar se o usuário tem acesso
            if (!$conversation->canBeAccessedBy(auth()->id())) {
                abort(403);
            }

            // Criar acordo de entrega
            $agreement = DeliveryAgreement::create([
                'conversation_id' => $request->conversation_id,
                'sub_order_id' => $request->sub_order_id,
                'proposed_by' => auth()->id(),
                'type' => $request->type,
                'description' => $request->description,
                'details' => $request->details,
                'delivery_fee' => $request->delivery_fee,
                'estimated_date' => $request->estimated_date,
                'estimated_time' => $request->estimated_time,
                'status' => 'proposed'
            ]);

            // Criar mensagem do sistema sobre a proposta
            Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => auth()->id(),
                'sender_type' => auth()->user()->isSeller() ? 'seller' : 'customer',
                'type' => 'delivery_proposal',
                'content' => "Nova proposta de entrega: {$agreement->getTypeLabel()}",
                'delivery_info' => [
                    'agreement_id' => $agreement->id,
                    'type' => $agreement->type,
                    'description' => $agreement->description,
                    'fee' => $agreement->delivery_fee,
                    'date' => $agreement->estimated_date,
                    'time' => $agreement->estimated_time
                ]
            ]);

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'agreement' => $agreement->load('proposer')
                ]);
            }

            return back()->with('success', 'Proposta de entrega enviada!');
            
        } catch (\Exception $e) {
            DB::rollback();
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Erro ao criar proposta: ' . $e->getMessage());
        }
    }

    /**
     * Aceitar acordo de entrega
     */
    public function accept(Request $request, DeliveryAgreement $agreement)
    {
        // Verificar acesso
        $conversation = $agreement->conversation;
        if (!$conversation->canBeAccessedBy(auth()->id())) {
            abort(403);
        }

        // Verificar se pode aceitar (não pode ser quem propôs)
        if ($agreement->proposed_by == auth()->id()) {
            return back()->with('error', 'Você não pode aceitar sua própria proposta.');
        }

        // Verificar se ainda pode ser aceito
        if (!$agreement->canBeAccepted()) {
            return back()->with('error', 'Este acordo não pode mais ser aceito.');
        }

        DB::beginTransaction();
        try {
            $agreement->accept();
            DB::commit();

            if ($request->ajax()) {
                return response()->json(['success' => true]);
            }

            return back()->with('success', 'Acordo de entrega aceito!');
            
        } catch (\Exception $e) {
            DB::rollback();
            
            if ($request->ajax()) {
                return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
            }
            
            return back()->with('error', 'Erro ao aceitar acordo: ' . $e->getMessage());
        }
    }

    /**
     * Rejeitar acordo de entrega
     */
    public function reject(Request $request, DeliveryAgreement $agreement)
    {
        $request->validate([
            'reason' => 'nullable|string|max:500'
        ]);

        // Verificar acesso
        $conversation = $agreement->conversation;
        if (!$conversation->canBeAccessedBy(auth()->id())) {
            abort(403);
        }

        // Verificar se pode rejeitar
        if ($agreement->proposed_by == auth()->id()) {
            return back()->with('error', 'Use a opção cancelar para desfazer sua proposta.');
        }

        if (!$agreement->canBeModified()) {
            return back()->with('error', 'Este acordo não pode mais ser rejeitado.');
        }

        DB::beginTransaction();
        try {
            $agreement->reject($request->reason);
            DB::commit();

            if ($request->ajax()) {
                return response()->json(['success' => true]);
            }

            return back()->with('success', 'Acordo de entrega rejeitado.');
            
        } catch (\Exception $e) {
            DB::rollback();
            
            if ($request->ajax()) {
                return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
            }
            
            return back()->with('error', 'Erro ao rejeitar acordo: ' . $e->getMessage());
        }
    }

    /**
     * Marcar entrega como concluída
     */
    public function complete(Request $request, DeliveryAgreement $agreement)
    {
        $request->validate([
            'proof' => 'nullable|array',
            'proof.photo' => 'nullable|image|max:5120',
            'proof.notes' => 'nullable|string|max:500'
        ]);

        // Verificar acesso
        $conversation = $agreement->conversation;
        if (!$conversation->canBeAccessedBy(auth()->id())) {
            abort(403);
        }

        // Verificar se pode ser concluído
        if (!$agreement->canBeCompleted()) {
            return back()->with('error', 'Este acordo não pode ser marcado como concluído.');
        }

        DB::beginTransaction();
        try {
            $proof = null;
            
            // Processar comprovante se houver
            if ($request->hasFile('proof.photo')) {
                $path = $request->file('proof.photo')->store('delivery-proofs', 'public');
                $proof = [
                    'photo' => $path,
                    'notes' => $request->input('proof.notes'),
                    'completed_by' => auth()->id(),
                    'completed_at' => now()
                ];
            }

            $agreement->markAsCompleted($proof);
            DB::commit();

            if ($request->ajax()) {
                return response()->json(['success' => true]);
            }

            return back()->with('success', 'Entrega concluída com sucesso!');
            
        } catch (\Exception $e) {
            DB::rollback();
            
            if ($request->ajax()) {
                return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
            }
            
            return back()->with('error', 'Erro ao concluir entrega: ' . $e->getMessage());
        }
    }

    /**
     * Cancelar acordo de entrega
     */
    public function cancel(Request $request, DeliveryAgreement $agreement)
    {
        // Verificar acesso
        $conversation = $agreement->conversation;
        if (!$conversation->canBeAccessedBy(auth()->id())) {
            abort(403);
        }

        // Verificar se é quem propôs
        if ($agreement->proposed_by != auth()->id()) {
            return back()->with('error', 'Apenas quem propôs pode cancelar o acordo.');
        }

        // Verificar se pode ser cancelado
        if (!$agreement->canBeModified()) {
            return back()->with('error', 'Este acordo não pode mais ser cancelado.');
        }

        DB::beginTransaction();
        try {
            $agreement->cancel();
            
            // Criar mensagem do sistema
            Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => auth()->id(),
                'sender_type' => 'system',
                'type' => 'system',
                'content' => 'Proposta de entrega foi cancelada.'
            ]);
            
            DB::commit();

            if ($request->ajax()) {
                return response()->json(['success' => true]);
            }

            return back()->with('success', 'Acordo cancelado.');
            
        } catch (\Exception $e) {
            DB::rollback();
            
            if ($request->ajax()) {
                return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
            }
            
            return back()->with('error', 'Erro ao cancelar acordo: ' . $e->getMessage());
        }
    }
}
