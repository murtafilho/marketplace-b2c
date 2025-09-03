<?php
/**
 * Arquivo: app/Http/Controllers/ConversationController.php
 * Descrição: Controller para gerenciar conversas entre compradores e vendedores
 * Laravel Version: 12.x
 * Criado em: 03/01/2025
 */

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Product;
use App\Models\Order;
use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ConversationController extends Controller
{
    /**
     * Lista todas as conversas do usuário
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $userType = $user->isSeller() ? 'seller' : 'customer';
        
        $conversations = Conversation::query()
            ->when($user->isSeller(), function ($query) use ($user) {
                return $query->where('seller_id', $user->id);
            }, function ($query) use ($user) {
                return $query->where('customer_id', $user->id);
            })
            ->with(['customer', 'sellerUser', 'product', 'order', 'lastMessage'])
            ->withCount(['messages'])
            ->orderBy('last_message_at', 'desc')
            ->paginate(20);

        return view('conversations.index', compact('conversations', 'userType'));
    }

    /**
     * Mostra uma conversa específica
     */
    public function show(Conversation $conversation)
    {
        // Verificar se o usuário tem acesso à conversa
        if (!$conversation->canBeAccessedBy(auth()->id())) {
            abort(403, 'Você não tem permissão para acessar esta conversa.');
        }

        // Marcar mensagens como lidas
        $userType = auth()->user()->isSeller() ? 'seller' : 'customer';
        $conversation->markAsReadFor($userType);

        // Carregar mensagens e relacionamentos
        $conversation->load([
            'customer',
            'sellerUser',
            'product',
            'order',
            'messages' => function ($query) {
                $query->with('sender')->orderBy('created_at', 'asc');
            },
            'deliveryAgreements' => function ($query) {
                $query->latest();
            }
        ]);

        return view('conversations.show', compact('conversation', 'userType'));
    }

    /**
     * Exibe formulário para criar nova conversa
     */
    public function createForm(Request $request)
    {
        $productId = $request->get('product_id');
        $sellerId = $request->get('seller_id');
        
        $product = null;
        $seller = null;
        
        if ($productId) {
            $product = Product::findOrFail($productId);
            $seller = $product->sellerUser;
        } elseif ($sellerId) {
            $seller = User::findOrFail($sellerId);
        }
        
        return view('conversations.create', compact('product', 'seller'));
    }
    
    /**
     * Inicia uma nova conversa
     */
    public function create(Request $request)
    {
        $request->validate([
            'seller_id' => 'required_without:product_id|exists:seller_profiles,id',
            'product_id' => 'required_without:seller_id|exists:products,id',
            'order_id' => 'nullable|exists:orders,id',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|max:5000'
        ]);

        DB::beginTransaction();
        try {
            $user = auth()->user();
            
            // Se foi passado product_id, buscar o seller_id
            if ($request->product_id && !$request->seller_id) {
                $product = Product::findOrFail($request->product_id);
                $sellerId = $product->seller_id;
            } else {
                $sellerId = $request->seller_id;
                $product = null;
            }

            // Verificar se já existe conversa entre as partes sobre o mesmo produto/pedido
            $conversation = Conversation::where('customer_id', $user->id)
                ->where('seller_id', $sellerId)
                ->when($request->product_id, function ($query) use ($request) {
                    return $query->where('product_id', $request->product_id);
                })
                ->when($request->order_id, function ($query) use ($request) {
                    return $query->where('order_id', $request->order_id);
                })
                ->first();

            // Se não existe, criar nova conversa
            if (!$conversation) {
                $conversation = Conversation::create([
                    'uuid' => Str::uuid(),
                    'customer_id' => $user->id,
                    'seller_id' => $sellerId,
                    'product_id' => $request->product_id,
                    'order_id' => $request->order_id,
                    'subject' => $request->subject ?? $this->generateSubject($request),
                    'status' => 'active',
                    'priority' => 'normal'
                ]);
            }

            // Criar a primeira mensagem
            Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $user->id,
                'sender_type' => 'customer',
                'content' => $request->message,
                'type' => 'text',
                'status' => 'sent'
            ]);

            DB::commit();

            return redirect()->route('conversations.show', $conversation)
                ->with('success', 'Conversa iniciada com sucesso!');
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Erro ao iniciar conversa: ' . $e->getMessage());
        }
    }

    /**
     * Envia uma nova mensagem
     */
    public function sendMessage(Request $request, Conversation $conversation)
    {
        // Verificar acesso
        if (!$conversation->canBeAccessedBy(auth()->id())) {
            abort(403);
        }

        $request->validate([
            'content' => 'required_without:attachments|string|max:5000',
            'type' => 'in:text,image,document,delivery_proposal',
            'attachments.*' => 'nullable|file|max:10240', // 10MB max
            'delivery_info' => 'required_if:type,delivery_proposal|array'
        ]);

        DB::beginTransaction();
        try {
            $user = auth()->user();
            $senderType = $user->isSeller() ? 'seller' : 'customer';

            // Processar anexos se houver
            $attachments = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('conversation-attachments', 'public');
                    $attachments[] = [
                        'name' => $file->getClientOriginalName(),
                        'path' => $path,
                        'size' => $file->getSize(),
                        'mime' => $file->getMimeType()
                    ];
                }
            }

            // Criar mensagem
            $message = Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $user->id,
                'sender_type' => $senderType,
                'content' => $request->content ?? '',
                'type' => $request->type ?? 'text',
                'attachments' => !empty($attachments) ? $attachments : null,
                'delivery_info' => $request->delivery_info,
                'status' => 'sent'
            ]);

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message->load('sender')
                ]);
            }

            return back()->with('success', 'Mensagem enviada!');
            
        } catch (\Exception $e) {
            DB::rollback();
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Erro ao enviar mensagem: ' . $e->getMessage());
        }
    }

    /**
     * Arquiva uma conversa
     */
    public function archive(Conversation $conversation)
    {
        if (!$conversation->canBeAccessedBy(auth()->id())) {
            abort(403);
        }

        $conversation->update(['status' => 'archived']);

        return back()->with('success', 'Conversa arquivada!');
    }

    /**
     * Marca conversa como prioritária (apenas vendedores)
     */
    public function setPriority(Request $request, Conversation $conversation)
    {
        if (!auth()->user()->isSeller() || $conversation->seller_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'priority' => 'required|in:low,normal,high'
        ]);

        $conversation->update(['priority' => $request->priority]);

        return back()->with('success', 'Prioridade atualizada!');
    }

    /**
     * Gera assunto automático para a conversa
     */
    private function generateSubject($request)
    {
        if ($request->product_id) {
            $product = Product::find($request->product_id);
            return "Dúvida sobre: " . Str::limit($product->name, 50);
        }

        if ($request->order_id) {
            $order = Order::find($request->order_id);
            return "Pedido #" . $order->order_number;
        }

        return "Nova conversa";
    }

    /**
     * Busca conversas (AJAX)
     */
    public function search(Request $request)
    {
        $user = auth()->user();
        $query = $request->get('q');

        $conversations = Conversation::query()
            ->when($user->isSeller(), function ($q) use ($user) {
                return $q->where('seller_id', $user->id);
            }, function ($q) use ($user) {
                return $q->where('customer_id', $user->id);
            })
            ->where(function ($q) use ($query) {
                $q->where('subject', 'like', "%{$query}%")
                    ->orWhereHas('customer', function ($q) use ($query) {
                        $q->where('name', 'like', "%{$query}%");
                    })
                    ->orWhereHas('sellerUser', function ($q) use ($query) {
                        $q->where('name', 'like', "%{$query}%");
                    })
                    ->orWhereHas('product', function ($q) use ($query) {
                        $q->where('name', 'like', "%{$query}%");
                    });
            })
            ->with(['customer', 'sellerUser', 'product', 'lastMessage'])
            ->limit(10)
            ->get();

        return response()->json($conversations);
    }
}
