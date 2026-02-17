## Запуск в OpenServer (быстрая инструкция)

1. Документ-рут

- Укажите корнем домена папку `public` проекта: `c:\Users\jesin\website-about\public`.

2. Добавление домена в hosts (рекомендуется запустить скрипт как администратор)

- Вручную: добавьте строку в `C:\Windows\System32\drivers\etc\hosts`:

```
127.0.0.1 website-about.test
```

- Автоматически: запустите `openserver_add_vhost.bat` (как администратор). Скрипт добавит запись и запустит OpenServer при наличии пути к нему.

3. Виртуальный хост

- Скопируйте шаблон `openserver_vhost.conf.template` в конфиг OpenServer (или добавьте через панель OpenServer). Задайте `ServerName` = `website-about.test` и `DocumentRoot` = путь к `public`.

4. Laravel-готовность (в каталоге проекта)

```bash
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install
npm run build
```

5. Перезапустите OpenServer и откройте http://website-about.test

Примечания:

- Скрипт изменяет `hosts` — требуется запуск от имени администратора.
- Если вы используете другой путь для OpenServer, укажите его при запуске скрипта.
