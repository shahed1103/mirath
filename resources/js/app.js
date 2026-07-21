import './bootstrap';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.axios.defaults.headers.common['X-CSRF-TOKEN'] =
document.querySelector('meta[name="csrf-token"]').content;

window.Echo = new Echo({

    broadcaster: 'reverb',

    key: import.meta.env.VITE_REVERB_APP_KEY,

    wsHost: window.location.hostname,

    wsPort: 8090,

    forceTLS:false,

    enabledTransports:['ws'],

    authEndpoint:'/broadcasting/auth',

});


console.log("Echo initialized");