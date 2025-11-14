<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>404 | Page Not Found</title>
    <meta name="description" content="404 — The page you're looking for can't be found.">
    <style>
        :root{
            --bg1: #ffcc01;
            --bg2: #ffde59;
            --accent: #ef4036;
            --accent-2: #2a75bb;
            --muted: #2d2d2d;
            --radius: 20px;
            --maxw: 960px;
        }

        *{box-sizing:border-box}
        html,body{height:100%}
        body{
            margin:0;
            font-family: 'Pokemon Solid', 'Arial Black', sans-serif;
            background: linear-gradient(180deg,var(--bg1) 0%, var(--bg2) 100%);
            color: #2d2d2d;
            display:flex;
            align-items:center;
            justify-content:center;
            padding:48px;
        }

        @font-face {
            font-family: 'Pokemon Solid';
            src: url('https://fonts.cdnfonts.com/s/16174/Pokemon%20Solid.woff') format('woff');
        }

        .wrap{
            width:100%;
            max-width:var(--maxw);
            display:grid;
            grid-template-columns: 1fr 420px;
            gap:32px;
            align-items:center;
        }

        .card{
            background:#fff;
            border-radius:var(--radius);
            padding:36px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        h1{
            margin:0 0 12px 0;
            font-size: clamp(36px, 6vw, 56px);
            line-height:1;
            color: var(--accent);
            text-shadow: 2px 2px 0 #2a75bb;
        }

        .code{
            color: var(--accent-2);
        }

        p.lead{
            margin:10px 0 20px 0;
            color:var(--muted);
            font-size:16px;
        }

        .actions{
            display:flex;
            gap:12px;
            flex-wrap:wrap;
            margin-top:18px;
        }

        a.btn{
            display:inline-flex;
            align-items:center;
            gap:10px;
            text-decoration:none;
            padding:12px 16px;
            border-radius:12px;
            font-weight:600;
            background:var(--accent);
            color:#fff;
            box-shadow: 0 6px 12px rgba(0,0,0,0.2);
            transition: transform .13s ease;
        }
        a.btn:active{transform:translateY(1px)}

        a.ghost{
            padding:12px 16px;
            border-radius:12px;
            color:var(--accent-2);
            background:transparent;
            border:2px solid var(--accent-2);
            text-decoration:none;
            font-weight:600;
        }

        .right{
            display:flex;
            align-items:center;
            justify-content:center;
            position:relative;
        }

        .pikachu{
            width:220px;
            aspect-ratio:1/1;
            background:url('https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/25.png') center/contain no-repeat;
            animation: bounce 2s infinite ease-in-out;
        }

        @keyframes bounce {
            0%,100%{transform:translateY(0)}
            50%{transform:translateY(-20px)}
        }

        .note{margin-top:18px;font-size:13px;color:var(--muted)}

        @media (max-width:880px){
            .wrap{grid-template-columns:1fr;}
            .right{order:-1;margin-bottom:12px}
        }
    </style>
</head>
<body>
<main class="wrap" role="main">
    <section class="card left" aria-labelledby="title">
        <h1 id="title"><span class="code">404</span> — Wild PIKA Appeared!</h1>
        <p class="lead">Looks like this page fainted! Don’t worry, you can run back home or try searching again.</p>

        <div class="actions" role="navigation">
            <a href="index.php?page=homepage2" class="btn">Back to homepage</a>
            <a href="javascript:history.back()" class="ghost">⬅️Go back</a>
        </div>
    </section>

    <aside class="card right" aria-hidden="true">
        <div class="pikachu" role="img" aria-label="Pikachu sprite"></div>
    </aside>
</main>
</body>
</html>
