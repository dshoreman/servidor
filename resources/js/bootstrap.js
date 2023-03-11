import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import _ from 'lodash';
import axios from 'axios';

window._ = _;
window.axios = axios;
window.axios.defaults.withCredentials = true;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/* global process */
window.Echo = new Echo({
    broadcaster: 'pusher',
    client: new Pusher(process.env.MIX_PUSHER_APP_KEY, {
        cluster: process.env.MIX_PUSHER_APP_CLUSTER ?? 'eu',
        forceTLS: true,
        authorizer: channel => ({
            authorize: (socketId, callback) => {
                axios.post('/api/broadcasting/auth', {
                    channel_name: channel.name,
                    socket_id: socketId,
                }).then(response => callback(false, response.data))
                    .catch(error => callback(true, error));
            },
        }),
    }),
});
