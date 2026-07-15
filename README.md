# 🎨 Personalized Item Branding Shop

> An e-commerce platform where customers **generate graphics from text prompts with AI (Stable Diffusion)** and **preview them on interactive 3D product models (Three.js)** before ordering personalized items.

<p>
  <img alt="Laravel" src="https://img.shields.io/badge/Laravel-11-FF2D20?logo=laravel&logoColor=white">
  <img alt="Vue" src="https://img.shields.io/badge/Vue.js-3-4FC08D?logo=vuedotjs&logoColor=white">
  <img alt="Inertia" src="https://img.shields.io/badge/Inertia.js-1.x-9553E9">
  <img alt="Three.js" src="https://img.shields.io/badge/Three.js-r170-000000?logo=threedotjs&logoColor=white">
  <img alt="Tailwind" src="https://img.shields.io/badge/Tailwind_CSS-3-06B6D4?logo=tailwindcss&logoColor=white">
  <img alt="Stable Diffusion" src="https://img.shields.io/badge/AI-Stable_Diffusion-purple">
  <img alt="PHP" src="https://img.shields.io/badge/PHP-8.2+-777BB4?logo=php&logoColor=white">
</p>

---

## 📖 About

This project was built as an **engineering thesis** ("Praca dyplomowa inżynierska") at the **Poznań University of Technology**, Faculty of Materials Engineering and Technical Physics (2025), by **Mateusz Borsuk** (supervisor: *dr inż. Marcin Borowski*).

The thesis — *"A sales platform for products with illustrations generated using artificial intelligence models"* — explores how **generative AI** and **3D visualization** can be combined into a modern, personalized e-commerce experience.

Instead of choosing from a fixed catalog, the user:

1. describes an image in natural language,
2. generates it with a **Stable Diffusion** model,
3. maps it onto a **3D product model** and personalizes it (logos, position, scale, rotation),
4. saves the design and orders the finished, branded item.

---

## ✨ Features

- 🔐 **Authentication & accounts** (Laravel Breeze) — registration, login, email verification, profile editing, password change, account deletion.
- 🖼️ **AI image generation** — turn a text prompt into a 512×512 graphic via the Stable Diffusion `txt2img` API.
- 🧊 **Interactive 3D visualization** — GLTF/GLB product models rendered with Three.js, HDR image-based lighting, and orbit camera controls.
- 🎯 **Graphic & logo mapping** — project the generated image and additional logos onto the model surface using UV coordinates, a raycaster for mouse picking, and 2D transforms (move / scale / rotate).
- 💾 **Save & reload designs** — persist a configured product to the database, together with an auto-generated preview screenshot.
- 🛒 **Shopping basket** — add saved designs to a cart and manage its contents.
- 🧾 **Order finalization** — place an order and download a generated **PDF order confirmation** (dompdf).
- 📱 **Responsive UI** — Tailwind CSS layout that works on desktop and mobile.

---

## 🔄 How it works

```
Prompt ──▶ Stable Diffusion (txt2img) ──▶ Generated graphic
                                              │
                       3D product model ◀─────┘ (mapped onto surface via UV + logos)
                                              │
                            Save design (+ preview screenshot)
                                              │
                              Basket ──▶ Finalize order ──▶ PDF confirmation
```

---

## 🧰 Tech stack

| Layer        | Technologies |
|--------------|--------------|
| **Frontend** | Vue 3, Inertia.js, Tailwind CSS, Vite (with SSR) |
| **3D**       | Three.js r170 (`GLTFLoader`, `RGBELoader`, `OrbitControls`, `Raycaster`) |
| **AI**       | Stable Diffusion (AUTOMATIC1111 WebUI `txt2img` API) |
| **Backend**  | Laravel 11, Inertia (server-side), Sanctum, Ziggy |
| **Database** | MySQL / MariaDB |
| **PDF**      | `barryvdh/laravel-dompdf` |
| **Tooling**  | Composer, npm, Laravel Pint, PHPUnit, Docker |

---

## ✅ Requirements

- **PHP 8.2+** and **Composer**
- **Node.js 18+** and **npm**
- **MySQL 8** or **MariaDB 10.11**
- **[AUTOMATIC1111 Stable Diffusion WebUI](https://github.com/AUTOMATIC1111/stable-diffusion-webui)** running locally with the API enabled — **required** for image generation (see [below](#-stable-diffusion-setup-required-for-ai-generation))
- **3D product assets** (`.glb` models + `.hdr` environment) — **not bundled** with the repo (see [below](#-3d-product-assets-not-included))

---

## 🚀 Getting started

### Option A — Docker (recommended)

A Docker Compose setup ships with the project (PHP-FPM, Nginx, MariaDB, Redis).

```bash
cp .env.example .env
```

Set the container hostnames in `.env` (the app reaches services by their compose name, not `127.0.0.1`):

```dotenv
DB_HOST=db
DB_PORT=3306
DB_DATABASE=polishop
DB_USERNAME=laravel
DB_PASSWORD=secret

REDIS_HOST=redis
REDIS_PORT=6379
```

Then build and start:

```bash
docker compose up -d --build
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
docker compose exec app php artisan storage:link
```

The app is served at **http://localhost:8000**. MariaDB is published on host port **3307** (→ 3306 internally) to avoid clashing with a local MySQL on 3306.

### Option B — Local / native

```bash
# 1. Install dependencies
composer install
npm install

# 2. Environment
cp .env.example .env
php artisan key:generate
# edit .env → set DB_CONNECTION / DB_DATABASE / DB_USERNAME / DB_PASSWORD

# 3. Database
php artisan migrate

# 4. Make storage assets (models, HDR, screenshots) publicly accessible
php artisan storage:link

# 5. Build the front-end (or use `npm run dev` while developing)
npm run build

# 6. Run
php artisan serve
```

Then open **http://localhost:8000**.

---

## 🤖 Stable Diffusion setup (required for AI generation)

The image generator calls a local **AUTOMATIC1111 Stable Diffusion WebUI** instance directly from the browser:

```
POST http://127.0.0.1:7860/sdapi/v1/txt2img
```

Request parameters used by the app:

| Parameter        | Value                    |
|------------------|--------------------------|
| `width`×`height` | 512 × 512                |
| `steps`          | 30                       |
| `cfg_scale`      | 7.5                      |
| `sampler_name`   | `Euler a`                |
| `negative_prompt`| `low quality, blurry`    |

**Launch the WebUI with the API and CORS enabled**, e.g.:

```bash
./webui.sh --api --cors-allow-origins=http://localhost:8000
```

> ℹ️ The endpoint is currently **hardcoded** in [`resources/js/Components/Slide1.vue`](resources/js/Components/Slide1.vue). If your WebUI runs on a different host/port, update it there. Making it configurable via an environment variable is on the roadmap.

---

## 🧊 3D product assets (not included)

3D models and the lighting environment are loaded from the public storage disk and are **not committed** to the repository. Add your own before running:

```
storage/app/public/
├── models/
│   └── <model-name>/
│       └── scene.glb          →  served as /storage/models/<model-name>/scene.glb
└── images/
    └── symmetrical_garden_02_2k.hdr   →  HDR environment for image-based lighting
```

Run `php artisan storage:link` so these are exposed under `/storage/...`.

---

## 🗄️ Database schema (overview)

| Table      | Purpose |
|------------|---------|
| `users`    | Accounts (Laravel Breeze) |
| `items`    | Saved product designs — chosen model, prompt/description, preview screenshot, unit price |
| `baskets`  | Shopping-cart entries |
| `orders`   | Placed orders (used to render the PDF confirmation) |

---

## 📁 Project structure

```
app/Http/Controllers/   Editor, Item, Screenshot, Basket, Order, Finalization, YourShop, Profile…
routes/web.php          Inertia pages & protected app routes
routes/api.php          Items, screenshots, basket & order endpoints
resources/js/
├── Pages/              Create, Editor, YourShop, Finalization, Auth/*, Profile/*
└── Components/         Carousel + Slide1 (AI prompt), Slide2/3 (3D viewer), Basket, ProductCard…
database/migrations/    users, items, baskets, orders (+ description/screenshot/unit_price)
docker/                 Nginx vhost for the Docker setup
```

---

## 🗺️ Roadmap

Features identified in the thesis as future work / not yet implemented:

- 💳 Online payment integration
- 📜 Order history
- ❓ FAQ page & customer-support system
- 🚚 Logistics / shipping integration
- 🧢 More 3D product models (e.g. cap, hoodie)
- 🌍 Prompt translation + full English localization
- 🎛️ Expanded Stable Diffusion controls and a configurable API endpoint
