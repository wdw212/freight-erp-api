@servers(['web' => ['root@124.222.232.138']])

@setup
    $appDir = '/www/wwwroot/124.222.232.138_82/freight-erp-api';
    $backupDir = '/www/backup/freight-erp-api';
    $timestamp = date('YmdHis');
@endsetup

{{-- 仅拉代码，不迁移（日常热更新） --}}
@task('deploy', ['on' => 'web'])
cd {{ $appDir }}
git pull
php artisan config:cache
php artisan route:cache
@endtask

{{-- 拉代码 + 依赖 + 迁移（有新依赖或迁移时使用） --}}
@task('deploy-update', ['on' => 'web'])
cd {{ $appDir }}
git pull
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
@endtask

{{-- 完整部署：备份 DB + 拉代码 + 迁移 + 快照回填（首次上线快照功能时使用） --}}
@task('deploy-full', ['on' => 'web'])
echo "=== [1/5] 备份数据库 ==="
mkdir -p {{ $backupDir }}
mysqldump -u root -p$(grep DB_PASSWORD {{ $appDir }}/.env | cut -d '=' -f2) \
    $(grep DB_DATABASE {{ $appDir }}/.env | cut -d '=' -f2) \
    > {{ $backupDir }}/db_{{ $timestamp }}.sql
echo "备份完成: {{ $backupDir }}/db_{{ $timestamp }}.sql"

echo "=== [2/5] 拉取最新代码 ==="
cd {{ $appDir }}
git pull

echo "=== [3/5] 安装依赖 ==="
composer install --no-dev --optimize-autoloader

echo "=== [4/5] 执行数据库迁移 ==="
php artisan migrate --force

echo "=== [5/5] 回填历史快照字段 ==="
php artisan snapshot:backfill

echo "=== 清理缓存 ==="
php artisan config:cache
php artisan route:cache

echo "=== 部署完成 ==="
@endtask

{{-- 回滚：从最新备份恢复数据库 --}}
@task('rollback', ['on' => 'web'])
echo "=== 查找最新备份 ==="
LATEST=$(ls -t {{ $backupDir }}/db_*.sql 2>/dev/null | head -1)
if [ -z "$LATEST" ]; then
    echo "错误：未找到备份文件，回滚终止"
    exit 1
fi
echo "将恢复备份：$LATEST"
DB_NAME=$(grep DB_DATABASE {{ $appDir }}/.env | cut -d '=' -f2)
DB_PASS=$(grep DB_PASSWORD {{ $appDir }}/.env | cut -d '=' -f2)
mysql -u root -p${DB_PASS} ${DB_NAME} < $LATEST
echo "=== 数据库已回滚到：$LATEST ==="

cd {{ $appDir }}
git log --oneline -5
echo "如需回退代码请手动执行: git reset --hard <commit>"
@endtask
