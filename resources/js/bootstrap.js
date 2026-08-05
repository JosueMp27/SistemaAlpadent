// Documentacion de archivo: Entrada JavaScript de Vite; prepara dependencias frontend compartidas para las vistas.
// Mantiene visible el proposito del archivo dentro del codigo fuente.
// Documentacion: Axios se guarda en window para que cualquier script Blade pueda reutilizarlo si lo necesita.
import axios from 'axios';
window.axios = axios;

// Documentacion: este header identifica las llamadas como AJAX ante Laravel y posibles middlewares.
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
