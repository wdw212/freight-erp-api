# Freight ERP 部署与接手说明

## 1. 当前仓库归属

本项目代码仓库已切换到以下地址：

- 后端：`https://github.com/wdw212/freight-erp-api.git`
- 前端：`https://github.com/wdw212/huoyun-erp.git`

旧仓库地址已不再作为主仓库使用：

- 后端旧地址：`https://github.com/lovemintblue/freight-erp-api.git`
- 前端旧地址：`https://gitee.com/helilifadacai/huoyun-erp.git`

本地仓库 remote 规范：

- `origin`：当前主仓库，指向 `wdw212`
- `legacy-origin`：旧仓库，仅保留历史参考

## 2. 服务器目录

- 后端线上目录：`/www/wwwroot/124.222.232.138_82/freight-erp-api`
- 前端线上目录：`/www/wwwroot/124.222.232.138_81`

当前服务器情况：

- 后端目录是 Git 仓库
- 前端目录不是 Git 仓库，走构建产物发布

## 3. 已接通的自动部署链路

### 3.1 后端

后端仓库已存在：

- [Envoy.blade.php](./Envoy.blade.php)
- [backend-deploy.yml](./.github/workflows/backend-deploy.yml)

触发方式：

- 推送到后端仓库 `main`
- 或手动触发 `Backend Deploy`

执行内容：

1. GitHub Actions 通过 SSH 登录服务器
2. 进入 `/www/wwwroot/124.222.232.138_82/freight-erp-api`
3. `git fetch / git pull --ff-only`
4. 根据 `deploy_mode` 决定是否：
   - `composer install`
   - `php artisan migrate --force`
   - `php artisan snapshot:backfill`
5. 执行：
   - `php artisan optimize:clear`
   - `php artisan optimize`
   - `php artisan queue:restart`

### 3.2 前端

前端仓库已存在：

- [frontend-deploy.yml](../huoyun-erp/.github/workflows/frontend-deploy.yml)

触发方式：

- 推送到前端仓库 `main`
- 或手动触发 `Frontend Deploy`

执行内容：

1. GitHub Actions 在云端执行 `npm install`
2. 执行 `npm run build:prod`
3. 将 `dist` 构建产物上传为 artifact
4. 通过 SSH / SCP 拷贝到服务器临时目录：
   - `/tmp/huoyun-erp-release`
5. 在服务器上备份当前站点目录
6. 使用 `rsync` 发布到：
   - `/www/wwwroot/124.222.232.138_81`

注意：

- 前端发布时保留 `.user.ini`
- 不再手改线上 `assets/*.js` 作为常规流程

## 4. GitHub Actions Secrets

### 4.1 后端仓库 `wdw212/freight-erp-api`

需要的 secrets：

- `BACKEND_DEPLOY_HOST`
- `BACKEND_DEPLOY_USER`
- `BACKEND_DEPLOY_PORT`
- `BACKEND_DEPLOY_SSH_KEY`

### 4.2 前端仓库 `wdw212/huoyun-erp`

需要的 secrets：

- `FRONTEND_DEPLOY_HOST`
- `FRONTEND_DEPLOY_USER`
- `FRONTEND_DEPLOY_PORT`
- `FRONTEND_DEPLOY_SSH_KEY`

## 5. SSH Key 说明

### 5.1 GitHub Actions -> 服务器

用于 Actions 登录服务器的 key 已生成并接入服务器：

- 公钥标识：`github-actions-deploy`

这把 key 的公钥已写入：

- `/root/.ssh/authorized_keys`

### 5.2 服务器 -> GitHub

后端服务器本身已有可访问仓库的 SSH key，因此后端目录能直接从：

- `git@github.com:wdw212/freight-erp-api.git`

拉代码。

## 6. 备份与回滚

### 6.1 后端工作区备份

在切换和清理服务器后端工作区前，已做备份：

- `/www/backup/freight-erp-api-worktree/server_worktree_20260312_222428.tar.gz`

### 6.2 前端站点备份

前端 workflow 每次发布前会打包备份：

- `/www/backup/huoyun-erp/site_root_YYYYMMDD_HHMMSS.tar.gz`

### 6.3 后端数据库备份

后端 `Envoy` 的 `deploy-full` 仍保留数据库备份逻辑。

## 7. 日常开发后如何上线

### 7.1 后端上线流程

1. 在本地后端仓库开发
2. 提交代码
3. 推送到：
   - `origin/main`
4. GitHub Actions 自动执行 `Backend Deploy`

如果是涉及迁移或依赖变更：

- 在 GitHub Actions 页面手动触发 `Backend Deploy`
- `deploy_mode` 选择：
  - `deploy-update`
  - 或 `deploy-full`

### 7.2 前端上线流程

1. 在本地前端仓库开发
2. 本地先自测
3. 提交代码
4. 推送到：
   - `origin/main`
5. GitHub Actions 自动执行 `Frontend Deploy`

## 8. 推荐测试流程

### 8.1 后端改动后

建议至少做：

1. 本地 `php -l` 语法检查
2. 能跑的单测 / 特性测试先跑
3. 如果是接口改动，优先自己打一次接口验证
4. 推送后检查 GitHub Actions 结果
5. 上线后再做一次页面冒烟

### 8.2 前端改动后

建议至少做：

1. 本地页面自测
2. 核心流程冒烟：
   - 新增
   - 复制
   - 修改
   - 上传后保存
3. 推送后查看 `Frontend Deploy`
4. 发布完成后到线上再点一遍关键路径

## 9. 线上验证建议

每次发版后，至少检查以下内容：

- GitHub Actions run 是绿色成功
- 后端接口可访问
- 前端站点能正常打开
- 关键页面无白屏 / 404 / 500
- 本次改动对应功能闭环可复现

推荐优先验证：

- 操作单据新增
- 操作单据复制
- 操作单据修改
- 上传文件后保存
- 开票管理 / 申请开票

## 10. 当前已知注意事项

1. 后端自动部署已跑通
2. 前端自动部署已接通，若后续再失败，优先看 `Frontend Deploy` 的 Actions 日志
3. 不建议同时手动触发前端 workflow 和 push 自动触发，容易重复发版
4. 不要再把 GitHub PAT 发到聊天里，用完立刻 revoke

## 11. 接手人最短行动路径

如果后面别人接手，只做这几步：

1. 克隆两个 `wdw212` 仓库
2. 确认本地 `origin` 指向 `wdw212`
3. 改代码并推到 `main`
4. 看 GitHub Actions
5. 上线后做关键页面冒烟

如果需要排查部署问题，优先看：

- GitHub Actions run 日志
- 服务器目录：
  - 后端：`/www/wwwroot/124.222.232.138_82/freight-erp-api`
  - 前端：`/www/wwwroot/124.222.232.138_81`
- 备份目录：
  - `/www/backup/freight-erp-api-worktree`
  - `/www/backup/huoyun-erp`
