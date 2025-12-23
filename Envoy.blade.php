@servers(['web' => ['root@124.222.232.138']])

@task('deploy', ['on' => 'web'])
cd /www/wwwroot/124.222.232.138_82/freight-erp-api
git pull
@endtask

@task('deploy-update', ['on' => 'web'])
cd /www/wwwroot/124.222.232.138_82/freight-erp-api
git pull
composer install
php artisan migrate
@endtask
