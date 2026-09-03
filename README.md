# Metabolica Mente A.S.D. — sito vetrina

Sito dell'associazione con **registrazione utenti**, **conferma email** e
**coupon personale** per la lezione di prova gratuita (scaricabile in PDF e in
immagine, e inviato per email).

Scritto solo con **PHP + Laravel 12 + Blade + Bootstrap 5**.
Niente Node, niente npm, niente framework JavaScript: Bootstrap è salvato
dentro `public/`, quindi il sito funziona anche senza connessione.

---

## Avviare il sito

Dalla cartella del progetto:

```bash
php artisan serve
```

Poi apri **http://localhost:8000**.

Se è la prima volta:

```bash
composer install
php artisan key:generate
php artisan migrate --seed
```

---

## Far partire le email davvero

Di base le email **non partono**: vengono scritte in
`storage/logs/laravel.log`, così puoi provare tutto senza configurare niente.

Per farle arrivare per davvero con Gmail c'è un comando che fa tutto lui:

```bash
php artisan asd:mail
```

Chiede l'indirizzo Gmail e la **password per le app**, scrive il `.env` da solo
e manda subito una mail di prova. La password non si vede mentre la digiti.

Prima però ti serve la password per le app di Google:

1. Attiva la **verifica in due passaggi** sull'account Gmail dell'associazione
   (`myaccount.google.com/security`). Senza questa, il passo 2 non esiste.
2. Crea una **password per le app** (`myaccount.google.com/apppasswords`):
   Google ti dà 16 lettere.

In alternativa puoi modificare a mano il `.env`:

```
MAIL_MAILER=smtp
MAIL_USERNAME=tuaemail@gmail.com
MAIL_PASSWORD=lesedicilettere      # tutte attaccate, senza spazi
MAIL_FROM_ADDRESS="tuaemail@gmail.com"
```

Per rimandare solo una mail di prova, quando è già tutto configurato:

```bash
php artisan asd:mail --prova=tuo@indirizzo.it
```

L'account Gmail è il **mittente**. Il destinatario è sempre l'indirizzo che il
cliente scrive quando si registra.

---

## Il promemoria "la camminata è oggi"

La mattina del giorno dell'appuntamento parte una mail a chi si è prenotato.
Non si manda mai due volte alla stessa persona.

Per provarlo subito, senza aspettare:

```bash
php artisan asd:promemoria --prova
```

Toglie `--prova` e le manda davvero. Con `--data=2026-09-20` controlla un altro giorno.

**Perché parta da solo serve lo scheduler di Laravel acceso.** Mentre sviluppi,
apri un secondo terminale e lascialo aperto:

```bash
php artisan schedule:work
```

Quando il sito sarà su un server vero, basta una riga di cron ogni minuto che
lanci `php artisan schedule:run`. Su Windows, la stessa cosa con Utilità di
pianificazione.

---

## Cambiare i testi e i dati dell'associazione

Nome della trainer, Instagram, WhatsApp, luogo del ritrovo, telefono, valore e
durata del coupon, cosa portare alla lezione: **stanno tutti nel file `.env`** e
in `config/asd.php`. Cambi lì e cambiano in tutto il sito, nelle email e sul
coupon.

Due elenchi in `config/asd.php` meritano attenzione:

- **`appuntamenti`** — giorni, orari e luoghi delle camminate. Aggiungine quanti
  vuoi: la sezione "I nostri appuntamenti" in home si aggiorna da sola. Se
  svuoti l'elenco, la sezione sparisce.
- **`recensioni`** — parte **vuota di proposito** e finché resta vuota la
  sezione "Dicono di noi" non compare. Mettici solo frasi vere di persone vere.

## Il vestito grafico

Colori e stile stanno tutti in `public/css/metabolica.css`, in cima al file
dentro `:root`. Cambi un colore lì e cambia in tutto il sito.

| Variabile | Colore | Dove si vede |
|---|---|---|
| `--mm-navy` | `#0c2f54` | titoli, footer, biglietto |
| `--mm-gold` | `#c89538` | bottoni, badge, bordi |
| `--mm-green` | `#15803d` | conferme, spunte |
| `--mm-cream` | `#faf8f5` | sfondo delle pagine |

Caratteri: **Outfit** per i titoli, **Plus Jakarta Sans** per il testo. Arrivano
da Google Fonts; se non c'è connessione il sito usa quelli di sistema e resta
leggibile. Bootstrap 5.3, le icone Bootstrap e i coriandoli sono invece salvati
dentro `public/`, quindi funzionano sempre.

---

## Le immagini

| File | A cosa serve |
|---|---|
| `public/img/logo.jpg` | Logo dell'associazione: navbar, footer, email, coupon |
| `public/img/uccellino.png` | *(facoltativo)* il pappagallino di Instagram, che compare nel prato disegnato sul coupon |

Se `uccellino.png` non c'è, il coupon viene disegnato lo stesso, solo senza
l'uccellino. Per aggiungerlo basta salvare il file con quel nome esatto in
`public/img/`.

---

## Area staff (convalida coupon)

La trainer può cercare un codice e "timbrare" il coupon, che da quel momento non
è più riutilizzabile. La pagina è `/staff/convalida` e si vede solo se l'utente
ha `is_staff = true`.

Per abilitare una persona:

```bash
php artisan asd:staff email@dellapersona.it
```

Il comando `php artisan migrate --seed` crea già un utente staff di prova:

- email: `trainer@metabolicamente.it`
- password: `CambiaQuesta123` ← **cambiala subito**

---

## Come è organizzato il codice

```
app/
  Http/Controllers/      PaginaController, CouponController,
                         Auth/ (registrazione, accesso, verifica email, password),
                         Admin/ConvalidaController
  Http/Middleware/       SoloStaff.php (protegge l'area staff)
  Models/                User.php, Coupon.php
  Mail/                  CouponMail.php, ContattoMail.php
  Notifications/         VerificaEmail.php, ReimpostaPassword.php
  Services/              CouponTicket.php  <-- disegna il biglietto (PNG + PDF)
config/asd.php           tutti i dati dell'associazione
resources/views/
  layouts/ partials/     struttura comune delle pagine
  pages/                 home, metodo, chi-siamo, contatti
  auth/                  registrazione, accesso, conferma email, password
  coupon/                area personale
  admin/                 convalida coupon
  emails/                le email (layout + coupon + verifica + password + contatto)
  pdf/                   il PDF del coupon
public/css/metabolica.css   colori e stile del sito
routes/web.php           tutti gli indirizzi del sito
lang/it/                 messaggi di errore dei form in italiano
```

---

## Regole del coupon

- **Uno solo per utente**: la colonna `user_id` della tabella `coupons` è
  `unique`, quindi il database stesso impedisce che se ne creino due.
- **Valido 30 giorni** dal giorno in cui viene generato (`config/asd.php`).
- **Usabile una volta sola**: dopo la convalida lo stato passa a `used`.
- **Nominativo**: nome e cognome sono stampati sul biglietto.
- Si scarica solo da collegati: nessuno può scaricare il coupon di un altro.
