import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import aspectRatio from '@tailwindcss/aspect-ratio';
import containerQueries from '@tailwindcss/container-queries';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            colors: {
                // Nova Paleta WCAG AA - Marketplace B2C
                primary: {
                    100: '#e6f2ff',    // Azul Muito Claro - Backgrounds
                    500: '#4a90e2',    // Azul Claro - Links/Hovers
                    700: '#2c5282',    // Azul Médio - Botões primários
                    900: '#1e3a5f',    // Azul Profundo - Headers/CTAs principais
                },
                secondary: {
                    100: '#fff5f0',    // Laranja Claro - Backgrounds vendedor
                    500: '#ff8c42',    // Laranja Médio - Badges/Alerts
                    700: '#ff6b35',    // Laranja Vibrante - CTAs vendedor
                },
                
                // Estados e Feedback com Contraste WCAG AA
                success: {
                    100: '#d1fae5',    // Verde Background
                    600: '#059669',    // Verde Sucesso
                },
                warning: {
                    100: '#fef3c7',    // Amarelo Background
                    600: '#d97706',    // Amarelo Warning
                },
                danger: {
                    100: '#fee2e2',    // Vermelho Background
                    600: '#dc2626',    // Vermelho Erro
                },
                
                // Neutros com Contraste WCAG AA
                gray: {
                    100: '#f5f5f5',    // Backgrounds alternativos
                    300: '#d4d4d4',    // Bordas
                    500: '#737373',    // Labels/Placeholders (5.5:1)
                    700: '#4a4a4a',    // Texto secundário (9.1:1)
                    900: '#1a1a1a',    // Texto principal (15.5:1)
                },
                
                // Cores Base
                white: '#ffffff',
                
                // Legacy (manter compatibilidade)
                'vale-verde': {
                    DEFAULT: '#2c5282',    // Mapear para primary-700
                    light: '#4a90e2',      // Mapear para primary-500
                    dark: '#1e3a5f',       // Mapear para primary-900
                },
                'sol-dourado': {
                    DEFAULT: '#ff8c42',    // Mapear para secondary-500
                    light: '#ff6b35',      // Mapear para secondary-700
                    dark: '#ff6b35',       // Mapear para secondary-700
                },
            },
            fontFamily: {
                sans: ['Inter var', 'Inter', 'system-ui', ...defaultTheme.fontFamily.sans],
            },
            animation: {
                'slide-up': 'slideUp 0.3s ease-out',
                'slide-down': 'slideDown 0.3s ease-out',
                'fade-in': 'fadeIn 0.2s ease-in',
                'fade-in-up': 'fadeInUp 0.3s ease-out',
                'bounce-subtle': 'bounceSubtle 0.6s ease-in-out',
                'pulse-gentle': 'pulseGentle 2s ease-in-out infinite',
            },
            keyframes: {
                slideUp: {
                    '0%': { transform: 'translateY(100%)', opacity: '0' },
                    '100%': { transform: 'translateY(0)', opacity: '1' },
                },
                slideDown: {
                    '0%': { transform: 'translateY(-100%)', opacity: '0' },
                    '100%': { transform: 'translateY(0)', opacity: '1' },
                },
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                fadeInUp: {
                    '0%': { opacity: '0', transform: 'translateY(20px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                bounceSubtle: {
                    '0%, 100%': { transform: 'translateY(0)' },
                    '50%': { transform: 'translateY(-10px)' },
                },
                pulseGentle: {
                    '0%, 100%': { opacity: '1' },
                    '50%': { opacity: '0.8' },
                },
            },
            screens: {
                'xs': '375px',
            },
            spacing: {
                '18': '4.5rem',
                '88': '22rem',
                '128': '32rem',
            },
            backdropBlur: {
                xs: '2px',
            },
            boxShadow: {
                'soft': '0 2px 15px 0 rgba(0, 0, 0, 0.1)',
                'card': '0 4px 20px 0 rgba(0, 0, 0, 0.08)',
                'elevated': '0 8px 30px 0 rgba(0, 0, 0, 0.12)',
            },
            borderRadius: {
                'xl': '0.75rem',
                '2xl': '1rem',
                '3xl': '1.5rem',
            },
        },
    },

    plugins: [
        forms,
        aspectRatio,
        containerQueries,
    ],
};