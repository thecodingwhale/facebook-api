#Laravel + React + Facebook API

Will get all your status post filter by a tag (currently I'm using `[grab]` for filtering the status post).

Currently using this starter react boilerplate <a href="https://github.com/Granze/react-starterify" target="_blank">https://github.com/Granze/react-starterify</a>.

For facebook api sdk integration with laravel <a href="https://github.com/SammyK/LaravelFacebookSdk" target="_blank">https://github.com/SammyK/LaravelFacebookSdk</a>.

##Quick Setup

1. Make sure you have <a href="http://laravel.com/docs/5.1/homestead" target="_blank">laravel homestead</a> installed.
2. Open your terminal and run `composer install`.
3. Duplicate `.env.example` to `.env` and run `php artisan key:generate`.
4. To setup you facebook api run this on your terminal `php artisan vendor:publish --provider="SammyK\LaravelFacebookSdk\LaravelFacebookSdkServiceProvider" --tag="config"`.
5. Open the `.env` and put your `FACEBOOK_APP_ID` and `FACEBOOK_APP_SECRET`.
6. Run `npm install` to install the npm packages. You must install this npm packages globally.
`npm install -g gulp bower bower-installer babel babel`