server {
    server_name {{ $site->primary_domain }};

    root {{ $site->document_root }};
    index index.php index.html index.htm;

    location / {
        try_files $uri $uri/ /index.php?query_string;

        location ~ \.php$ {
            try_files $uri =404;

            fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
            fastcgi_index index.php;
            fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;

            include fastcgi_params;
        }
    }

    location ~ /\.ht {
        deny all;
    }
}
