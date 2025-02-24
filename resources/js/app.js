import './bootstrap';
import.meta.glob([
    '../imgs/**',
    '../fonts/**',
]);

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();
