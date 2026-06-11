/**
 * Componente Alpine de la campana de notificaciones del navbar.
 *
 * - Muestra el nº de no leídas (badge), con valor inicial renderizado por el
 *   servidor y actualizado en vivo por WebSocket (canal de Laravel Notifications).
 * - Al abrir el dropdown carga las últimas notificaciones por AJAX.
 * - Permite marcar todas como leídas.
 *
 * Se registra en window para que Alpine lo encuentre en x-data="notificaciones()".
 */
window.notificaciones = (config = {}) => ({
    open: config.open ?? false,
    cargando: false,
    items: [],
    noLeidas: config.noLeidas ?? 0,
    userId: config.userId ?? null,

    init() {
        // Suscripción en vivo: cada notificación nueva sube el contador.
        if (this.userId && window.Echo) {
            window.Echo.private(`App.Models.User.${this.userId}`).notification(() => {
                this.noLeidas++;
                if (this.open) this.cargar();
            });
        }
    },

    toggle() {
        this.open = !this.open;
        if (this.open) this.cargar();
    },

    async cargar() {
        this.cargando = true;
        try {
            const res = await fetch('/notificaciones/recientes', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    Accept: 'application/json',
                },
            });
            const data = await res.json();
            this.items = data.notificaciones;
            this.noLeidas = data.no_leidas;
        } catch (e) {
            console.error(e);
        }
        this.cargando = false;
    },

    async marcarTodas() {
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
        try {
            await fetch('/notificaciones/leer-todas', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                    Accept: 'application/json',
                },
            });
            this.noLeidas = 0;
            this.items = this.items.map((n) => ({ ...n, leida: true }));
        } catch (e) {
            console.error(e);
        }
    },
});
