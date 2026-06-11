/**
 * Selector de día + hora para pedir cita en el taller.
 *
 * Recibe del servidor la lista de días con sus horas libres y deja elegir un
 * hueco. El valor final ("YYYY-MM-DD HH:MM") se vuelca en un input oculto
 * llamado fecha_cita, que es lo que se envía con el formulario.
 *
 * Se registra en window para que Alpine lo encuentre en x-data.
 */
window.agendaPicker = (dias = [], valorInicial = '') => ({
    dias,
    diaActivo: null,
    diaEtiqueta: '',
    horaActiva: null,
    slots: [],

    init() {
        // Restaura la selección si el formulario vuelve con errores de validación.
        if (valorInicial) {
            const [fecha, hora] = valorInicial.split(' ');
            const dia = this.dias.find((d) => d.fecha === fecha);
            if (dia) {
                this.elegirDia(dia);
                const slot = dia.slots.find((s) => s.hora === hora);
                if (slot && !slot.ocupado) {
                    this.horaActiva = hora;
                }
            }
        }
    },

    elegirDia(dia) {
        this.diaActivo = dia.fecha;
        this.diaEtiqueta = dia.etiqueta;
        this.slots = dia.slots;
        this.horaActiva = null;
    },

    elegirHora(hora) {
        this.horaActiva = hora;
    },

    // Valor que se envía en el input oculto.
    get seleccion() {
        return this.diaActivo && this.horaActiva ? `${this.diaActivo} ${this.horaActiva}` : '';
    },

    // Texto bonito para confirmar al usuario.
    get resumen() {
        return this.horaActiva ? `${this.diaEtiqueta} · ${this.horaActiva}` : '';
    },
});
