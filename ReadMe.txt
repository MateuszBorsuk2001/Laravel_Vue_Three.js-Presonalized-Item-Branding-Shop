Serwis wykorzystuje lokalnie dziłającoą instyancje Stble diffiusion pobraną z https://github.com/AUTOMATIC1111/stable-diffusion-webui.
W przypadku gdy krozystamy z karty graficznej firmy Nvidia wystarczy po zaisntalowaniu uruchomić webui-user.bat w celu umożliwienia generacji zdjęć.
Jeśli krozystamy z karty graficznej firmy AMD należy wykonać dodatkowe kroki przedstawione tym filmie instruktarzowym https://www.youtube.com/watch?v=n8RhNoAenvM (link aktywny na dzień 21.01.2025) autorstwa FE-Enginner.
Karty graficzne innych firm niż Nvidia i AMD nie są obsługiwane.
W obu przypadkach automatycznie uruchomiany podczas włączenia skryptu interfejs graficzny jest zbędny.
Stable Diffiusion generuję zdjęcia na podstawie wytreowanego modelu, w moejej apliokacji generuje zdjęcia przy użyciu modelu https://civitai.com/models/112902/dreamshaper-xl, ale niezależnie od wybranego modelu aplikacja bedzię działać poprawnie. 
Wybrany model należy pobrać i umieścić w foderze stable-diffusion-webui-directml\models\Stable-diffusion, model powinien być w formacie .safetensors.

Aplikacja wykorzystuje zapis pliku do pdf w celu udostępnienia tej funkcji należy odkomentować w miejscu isntalacji PHP, w pliku php.ini linię zawierającą extension=gd.

W celu uruchomnia aplikacji należy wykonać następujące kroki:
1. aktywować komende w terminalu php artisan serve
2. aktywować komende w terminalu npm run dev
3. Przygotować plik .env zgodnie z  plikiem .env.example
4  Uruchomic komende w terminalu php artisan migrate
5. Uruchomic komende w terminalu php artisan db:seed
6. Uruchomic komende w terminalu php artisan storage:link
7. Uruchomic komende w terminalu php artisan key:generate
8. Komenda db:seed utworzy użytkownika test@example.com o haśle password.

Głównym błędem w aplikacji jest błąd w przesyłaniu tokena CRFS między sesją użytkownika w przypadku wystąpienia tego błędu poinformuje nas o tym laravel wyświetlając błąd 419. W takim przypadku należy odświeżyć stronę.