<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>404 | Not Logged In</title>
    <meta name="description" content="Slightly Pokémon‑themed 404 / Not Logged In page in one file." />
    <style>
        :root{
            --bg: #0b1020;
            --panel: #111833;
            --text: #e8ecff;
            --muted: #9aa4c7;
            --accent: #e74c3c; /* poke red */
            --accent-2: #ffffff; /* poke white */
            --accent-3: #1c233b; /* dark ring */
            --shadow: 0 10px 30px rgba(0,0,0,.35);
            --radius: 18px;
        }
        @media (prefers-color-scheme: light) {
            :root{
                --bg: #f4f6ff;
                --panel: #ffffff;
                --text: #111528;
                --muted: #5a6284;
                --accent-3: #d8deff;
            }
        }
        * { box-sizing: border-box; }
        html, body { height: 100%; }
        body {
            margin: 0;
            font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial, "Noto Sans", "Apple Color Emoji", "Segoe UI Emoji";
            color: var(--text);
            background: radial-gradient(1200px 800px at 80% -10%, rgba(231,76,60,.08), transparent 60%),
            radial-gradient(900px 700px at 0% 100%, rgba(255,255,255,.06), transparent 50%), var(--bg);
            display: grid;
            place-items: center;
        }
        .card {
            width: min(880px, 92vw);
            background: var(--panel);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: clamp(20px, 4vw, 40px);
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255,255,255,.06);
        }
        .badge {
            position: absolute;
            top: 16px;
            right: 16px;
            font-size: 12px;
            letter-spacing: .12em;
            color: var(--muted);
            text-transform: uppercase;
        }
        .row { display: grid; grid-template-columns: 1fr 1.2fr; gap: clamp(18px, 3.5vw, 36px); align-items: center; }
        @media (max-width: 760px){ .row{ grid-template-columns: 1fr; } }

        h1 {
            font-size: clamp(28px, 4.4vw, 44px);
            line-height: 1.05;
            margin: 6px 0 10px;
        }
        .eyebrow {
            color: var(--muted);
            font-weight: 600;
            letter-spacing: .06em;
            text-transform: uppercase;
            font-size: 12px;
        }
        p { color: var(--muted); font-size: clamp(14px, 1.4vw, 16px); }

        .actions { display: flex; gap: 12px; flex-wrap: wrap; margin-top: 18px; }
        .btn {
            appearance: none; border: 0; cursor: pointer; padding: 12px 16px; border-radius: 12px; font-weight: 700;
            box-shadow: 0 6px 18px rgba(0,0,0,.15), inset 0 -2px 0 rgba(0,0,0,.15);
            transition: transform .08s ease, filter .2s ease;
        }
        .btn:active { transform: translateY(1px); }
        .btn-primary { background: linear-gradient(180deg, var(--accent) 0%, #cc3e30 100%); color: var(--accent-2); }
        .btn-ghost   { background: rgba(255,255,255,.06); color: var(--text); border: 1px solid rgba(255,255,255,.08); }

        /* Little monster-catching vibe: a subtle “pokéball” built with SVG and animated */
        .art {
            width: 100%;
            aspect-ratio: 1 / 1;
            max-width: 360px;
            margin: auto;
            display: grid;
            place-items: center;
            position: relative;
            filter: drop-shadow(0 12px 24px rgba(0,0,0,.25));
        }
        .pokeball { width: 100%; height: auto; }
        .spin { animation: spin 8s linear infinite; transform-origin: 50% 50%; }
        @keyframes spin { from { transform: rotate(0deg) } to { transform: rotate(360deg) } }

        /* Floating sparkle bits */
        .sparkle {
            position: absolute; inset: 0; pointer-events: none; overflow: hidden;
        }
        .sparkle i {
            position: absolute; width: 6px; height: 6px; border-radius: 50%; background: var(--accent-2); opacity: .7;
            animation: floatUp 6s ease-in infinite;
        }
        @keyframes floatUp {
            0% { transform: translateY(20px) scale(.7); opacity: 0; }
            10% { opacity: .7; }
            100% { transform: translateY(-220px) scale(1.2); opacity: 0; }
        }

        /* Footer */
        .foot {
            margin-top: 24px; font-size: 12px; color: var(--muted);
            display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap;
        }
        code { background: rgba(255,255,255,.06); padding: 3px 6px; border-radius: 6px; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace; }
    </style>
</head>
<body>
<main class="card" role="main" aria-labelledby="title">
    <div class="badge">Error 404</div>
    <div class="row">
        <section class="art" aria-hidden="true">
            <!-- Accessible decorative SVG vaguely poké‑inspired (no brand marks) -->
            <svg class="pokeball" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <defs>
                    <linearGradient id="red" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#ff6559"/>
                        <stop offset="100%" stop-color="#e74c3c"/>
                    </linearGradient>
                    <linearGradient id="white" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#ffffff"/>
                        <stop offset="100%" stop-color="#f1f5ff"/>
                    </linearGradient>
                    <filter id="innerShadow" x="-20%" y="-20%" width="140%" height="140%">
                        <feOffset dx="0" dy="2"/>
                        <feGaussianBlur stdDeviation="2" result="offset-blur"/>
                        <feComposite operator="out" in="SourceGraphic" in2="offset-blur" result="inverse"/>
                        <feFlood flood-color="#000" flood-opacity="0.25" result="color"/>
                        <feComposite operator="in" in="color" in2="inverse" result="shadow"/>
                        <feComposite operator="over" in="shadow" in2="SourceGraphic"/>
                    </filter>
                </defs>

                <!-- Outer ring -->
                <g class="spin">
                    <circle cx="100" cy="100" r="96" fill="#0e1530" opacity="0.12"/>
                    <circle cx="100" cy="100" r="86" fill="#101730"/>
                    <!-- Top half -->
                    <path d="M100 14 A86 86 0 0 1 186 100 H14 A86 86 0 0 1 100 14z" fill="url(#red)"/>
                    <!-- Bottom half -->
                    <path d="M14 100 H186 A86 86 0 0 1 100 186 A86 86 0 0 1 14 100z" fill="url(#white)"/>
                    <!-- Middle band -->
                    <rect x="14" y="92" width="172" height="16" rx="8" fill="#1c233b"/>
                    <!-- Center button -->
                    <circle cx="100" cy="100" r="26" fill="#1c233b"/>
                    <circle cx="100" cy="100" r="18" fill="#ffffff" filter="url(#innerShadow)"/>
                </g>
            </svg>
            <div class="sparkle" aria-hidden="true"></div>
        </section>

        <section>
            <div class="eyebrow">Oops! Trainers only</div>
            <h1 id="title">You’re not logged in.</h1>
            <p>
                The page you’re trying to catch is protected. Log in to continue, or head back to safety.
            </p>
            <div class="actions">
                <button class="btn btn-primary" id="loginBtn" aria-label="Log in">Log in</button>
                <button class="btn btn-ghost" id="homeBtn" aria-label="Go home">Go home</button>
            </div>
            <div class="foot">
                <span id="time"></span>
            </div>
        </section>
    </div>
</main>

<script>
    // Sparkle generator (decorative)
    (()=>{
        const wrap = document.querySelector('.sparkle');
        for(let i=0;i<24;i++){
            const dot = document.createElement('i');
            dot.style.left = Math.random()*100 + '%';
            dot.style.bottom = (Math.random()*-40) + 'px';
            dot.style.animationDelay = (Math.random()*6)+'s';
            dot.style.animationDuration = (5 + Math.random()*5)+'s';
            wrap.appendChild(dot);
        }
    })();

    // Helper: read URL params for simple redirects
    const params = new URLSearchParams(location.search);
    const loginTarget = params.get('login') || './index.php?page=login';
    const homeTarget  = params.get('home')  || './index.php?page=homepage2';

    document.getElementById('loginBtn').addEventListener('click',()=>{
        window.location.href = loginTarget;
    });
    document.getElementById('homeBtn').addEventListener('click',()=>{
        window.location.href = homeTarget;
    });

    // Cute real-time clock (local)
    const timeEl = document.getElementById('time');
    const fmt = new Intl.DateTimeFormat(undefined,{hour:'2-digit',minute:'2-digit',second:'2-digit'});
    setInterval(()=>{ timeEl.textContent = 'Local time: ' + fmt.format(new Date()); }, 500);

    // Keyboard shortcuts
    window.addEventListener('keydown', (e)=>{
        if(e.key.toLowerCase() === 'l') document.getElementById('loginBtn').click();
        if(e.key.toLowerCase() === 'h') document.getElementById('homeBtn').click();
    });
</script>
</body>
</html>
