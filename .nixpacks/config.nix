{ pkgs }:

{
  packages = [
    pkgs.php82
    pkgs.php82Extensions.pdo
    pkgs.php82Extensions.pdo_mysql
    pkgs.php82Extensions.mbstring
    pkgs.php82Extensions.tokenizer
    pkgs.php82Extensions.xml
    pkgs.php82Extensions.curl
    pkgs.php82Extensions.openssl
    pkgs.php82Extensions.fileinfo
    pkgs.php82Extensions.json
    pkgs.composer
  ];

  env = {
    APP_ENV = "production";
  };

  build = ''
    composer install --no-dev --optimize-autoloader
  '';

  start = "php artisan serve --host=0.0.0.0 --port=$PORT";
}
