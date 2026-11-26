<?php
declare(strict_types=1);

const ICONS=[
    'cart'=>'<circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/>',
    'package'=>'<path d="M11 21.73a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73z"/><path d="M12 22V12"/><path d="m3.3 7 8.7 5 8.7-5"/>',
    'grid'=>'<rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/>',
    'chart'=>'<path d="M3 3v16a2 2 0 0 0 2 2h16"/><path d="M18 17V9"/><path d="M13 17V5"/><path d="M8 17v-3"/>',
    'user'=>'<path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
    'logout'=>'<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5"/><path d="M21 12H9"/>',
    'shield'=>'<path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/>',
    'truck'=>'<path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><path d="M15 18H9"/><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/>',
    'refresh'=>'<path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/><path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"/><path d="M8 16H3v5"/>',
    'trash'=>'<path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>',
    'arrow'=>'<path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>',
    'check'=>'<path d="M20 6 9 17l-5-5"/>',
    'alert'=>'<circle cx="12" cy="12" r="10"/><path d="M12 8v4"/><path d="M12 16h.01"/>',
    'receipt'=>'<path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect width="8" height="4" x="8" y="2" rx="1"/><path d="M9 12h6"/><path d="M9 16h6"/>',
    'activity'=>'<path d="M22 12h-4l-3 9L9 3l-3 9H2"/>',
    'lock'=>'<rect width="18" height="11" x="3" y="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>',
    'chevron'=>'<path d="m9 18 6-6-6-6"/>',
    'info'=>'<circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/>',
    'wallet'=>'<path d="M19 7V4a1 1 0 0 0-1-1H5a2 2 0 0 0 0 4h15a1 1 0 0 1 1 1v4h-3a2 2 0 0 0 0 4h3a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1"/><path d="M3 5v14a2 2 0 0 0 2 2h15a1 1 0 0 0 1-1v-4"/>',
    'clock'=>'<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>',
    'bag'=>'<path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/>',
];
function icon(string $name,string $class='icon'): string {
    return '<svg class="'.h($class).'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">'.(ICONS[$name]??'').'</svg>';
}

function money(mixed $amount): string {return '<span class="money"><span class="amt">'.h($amount).'</span> <span class="cur">IDR_DUMMY</span></span>';}
function product_thumb(string $id,string $size='',?string $href=null): string {
    $hue=crc32($id)%360;$inner=icon('bag','thumb-icon').'<span class="thumb-code">'.h($id).'</span>';
    $attrs='class="thumb '.h($size).'" style="--hue:'.$hue.'"';
    return $href===null?'<div '.$attrs.' aria-hidden="true">'.$inner.'</div>':'<a '.$attrs.' href="'.h($href).'" tabindex="-1" aria-hidden="true">'.$inner.'</a>';
}
const STATUS_LABELS=['queued'=>['Menunggu','wait'],'processing'=>['Diproses','info'],'completed'=>['Selesai','ok'],'failed'=>['Gagal','bad']];
function status_badge(string $status): string {
    [$label,$tone]=STATUS_LABELS[$status]??['Status','info'];
    return '<span class="badge '.$tone.'"><span class="dot"></span>'.h($label).' <code>'.h($status).'</code></span>';
}
function format_time(?string $value): string {
    if(!$value)return '';
    try{$time=(new DateTimeImmutable($value))->setTimezone(new DateTimeZone('UTC'));}catch(Throwable){return h($value);}
    $months=['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    return '<time datetime="'.h($time->format(DATE_ATOM)).'">'.h($time->format('j').' '.$months[(int)$time->format('n')-1].' '.$time->format('Y, H:i').' UTC').'</time>';
}
function nav_link(string $href,string $label,string $iconName,string $current): string {
    $active=$current===$href||($href!=='/'&&str_starts_with($current,$href.'/'))||($href==='/products'&&$current==='/');
    return '<a class="nav-link" href="'.h($href).'"'.($active?' aria-current="page"':'').'>'.icon($iconName).'<span>'.h($label).'</span></a>';
}
const STYLES=<<<'CSS'
:root{--bg:#f5f7f6;--surface:#fff;--surface-2:#f0f4f3;--text:#10241f;--muted:#566a64;--border:#dfe7e4;--border-strong:#c3d1cc;--brand:#0f766e;--brand-strong:#115e59;--brand-soft:#e6f4f1;--on-brand:#fff;--accent:#c2410c;--accent-strong:#9a3412;--ok:#15803d;--ok-soft:#e8f6ec;--warn:#a16207;--warn-soft:#fdf6e3;--info:#1d4ed8;--info-soft:#eaf0fd;--bad:#b91c1c;--bad-soft:#fdeeee;--ring:#0f766e;--shadow-sm:0 1px 2px rgba(16,36,31,.06);--shadow:0 1px 2px rgba(16,36,31,.05),0 8px 24px -12px rgba(16,36,31,.18);--radius:14px;--radius-sm:10px;color-scheme:light}
@media (prefers-color-scheme:dark){:root{--bg:#0b1412;--surface:#111d1a;--surface-2:#16241f;--text:#e7efec;--muted:#9fb3ac;--border:#22332e;--border-strong:#2f4640;--brand:#2dd4bf;--brand-strong:#5eead4;--brand-soft:#123a34;--on-brand:#04201c;--accent:#fb923c;--accent-strong:#fdba74;--ok:#4ade80;--ok-soft:#12301d;--warn:#facc15;--warn-soft:#332a0c;--info:#93b4fd;--info-soft:#16233f;--bad:#f87171;--bad-soft:#3a1616;--ring:#5eead4;--shadow-sm:none;--shadow:0 1px 0 rgba(255,255,255,.03),0 10px 30px -14px rgba(0,0,0,.6);color-scheme:dark}}
*,*::before,*::after{box-sizing:border-box}
html{-webkit-text-size-adjust:100%}
body{margin:0;background:var(--bg);color:var(--text);font:16px/1.55 ui-sans-serif,system-ui,-apple-system,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif;-webkit-font-smoothing:antialiased;display:flex;flex-direction:column;min-height:100vh}
a{color:var(--brand);text-decoration:none}a:hover{text-decoration:underline;text-underline-offset:3px}
:focus-visible{outline:2px solid var(--ring);outline-offset:2px;border-radius:6px}
h1,h2,h3{line-height:1.2;letter-spacing:-.015em;margin:0}
h1{font-size:clamp(1.6rem,1.2rem + 1.4vw,2.15rem);font-weight:750}
h2{font-size:1.15rem;font-weight:700}
p{margin:0}
code{font:600 .72em/1 ui-monospace,SFMono-Regular,Menlo,monospace;opacity:.75}
.icon{width:20px;height:20px;flex:none}
.sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip-path:inset(50%);white-space:nowrap;border:0}
.container{width:100%;max-width:1180px;margin:0 auto;padding:0 20px}
.topbar{background:var(--brand-strong);color:#fff;font-size:.82rem}
@media (prefers-color-scheme:dark){.topbar{background:#0d2f2a;color:var(--text)}}
.topbar .container{display:flex;gap:8px;align-items:center;justify-content:center;min-height:36px;padding-top:6px;padding-bottom:6px;text-align:center}
.topbar .icon{width:16px;height:16px}
.site-header{position:sticky;top:0;z-index:20;background:color-mix(in srgb,var(--surface) 88%,transparent);backdrop-filter:saturate(160%) blur(10px);border-bottom:1px solid var(--border)}
.header-row{display:flex;align-items:center;gap:24px;min-height:68px}
.brand{display:inline-flex;align-items:center;gap:10px;color:var(--text);font-weight:800;font-size:1.2rem;letter-spacing:-.02em}
.brand:hover{text-decoration:none}
.brand-mark{display:grid;place-items:center;width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,var(--brand),#14b8a6);color:#fff;box-shadow:var(--shadow-sm)}
.brand-mark .icon{width:20px;height:20px}
.brand b{color:var(--brand)}
.main-nav{display:flex;gap:4px;flex:1}
.nav-link{display:inline-flex;align-items:center;gap:8px;padding:9px 12px;border-radius:999px;color:var(--muted);font-weight:600;font-size:.95rem;transition:background .18s,color .18s}
.nav-link:hover{background:var(--surface-2);color:var(--text);text-decoration:none}
.nav-link[aria-current=page]{background:var(--brand-soft);color:var(--brand-strong)}
@media (prefers-color-scheme:dark){.nav-link[aria-current=page]{color:var(--brand)}}
.header-actions{display:flex;align-items:center;gap:8px;margin-left:auto}
.btn{--b:var(--surface);--c:var(--text);--bd:var(--border-strong);display:inline-flex;align-items:center;justify-content:center;gap:8px;min-height:44px;padding:10px 18px;border:1px solid var(--bd);border-radius:var(--radius-sm);background:var(--b);color:var(--c);font:inherit;font-weight:650;font-size:.95rem;line-height:1.1;cursor:pointer;text-decoration:none;white-space:nowrap;transition:background .18s,border-color .18s,box-shadow .18s,color .18s}
.btn:hover{text-decoration:none;border-color:var(--brand);color:var(--brand)}
.btn-primary{--b:var(--brand);--c:var(--on-brand);--bd:var(--brand);box-shadow:var(--shadow-sm)}
.btn-primary:hover{background:var(--brand-strong);border-color:var(--brand-strong);color:var(--on-brand)}
.btn-accent{--b:var(--accent);--c:#fff;--bd:var(--accent)}
.btn-accent:hover{background:var(--accent-strong);border-color:var(--accent-strong);color:#fff}
@media (prefers-color-scheme:dark){.btn-accent{--c:#1c0d04}.btn-accent:hover{color:#1c0d04}}
.btn-ghost{--b:transparent;--bd:transparent;--c:var(--muted)}
.btn-ghost:hover{background:var(--surface-2);border-color:transparent;color:var(--text)}
.btn-danger:hover{color:var(--bad);background:var(--bad-soft);border-color:transparent}
.btn-sm{min-height:38px;padding:8px 12px;font-size:.88rem}
.btn-block{width:100%}
.btn-lg{min-height:52px;font-size:1.02rem;padding:12px 22px}
.cart-link{position:relative}
.account{display:inline-flex;align-items:center;gap:8px;color:var(--muted);font-size:.9rem;font-weight:600}
.avatar{display:grid;place-items:center;width:36px;height:36px;border-radius:999px;background:var(--brand-soft);color:var(--brand)}
.avatar .icon{width:18px;height:18px}
form.inline{display:inline}
.page{flex:1;padding:28px 0 64px}
.crumbs{display:flex;flex-wrap:wrap;align-items:center;gap:6px;font-size:.88rem;color:var(--muted);margin-bottom:14px}
.crumbs .icon{width:14px;height:14px}
.crumbs a{color:var(--muted)}.crumbs a:hover{color:var(--brand)}
.page-head{display:flex;flex-wrap:wrap;align-items:flex-end;justify-content:space-between;gap:12px 24px;margin-bottom:24px}
.page-head .lead{color:var(--muted);margin-top:6px;max-width:62ch}
.panel{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);box-shadow:var(--shadow-sm);padding:24px}
.panel+.panel:not(.summary){margin-top:16px}
.panel-title{display:flex;align-items:center;gap:10px;margin-bottom:16px}
.panel-title .icon{color:var(--brand)}
.muted{color:var(--muted)}
.fine{font-size:.84rem;color:var(--muted)}
.money{font-variant-numeric:tabular-nums;white-space:nowrap}
.money .cur{font-size:.68em;font-weight:700;letter-spacing:.04em;color:var(--muted);vertical-align:.12em}
.hero{position:relative;overflow:hidden;border-radius:20px;padding:36px clamp(20px,4vw,48px);margin-bottom:28px;background:radial-gradient(120% 140% at 100% 0%,#14b8a6 0%,transparent 55%),linear-gradient(135deg,#115e59,#0f766e 60%,#0d9488);color:#fff}
.hero h1{color:#fff;max-width:20ch}
.hero p{color:rgba(255,255,255,.86);margin-top:10px;max-width:56ch}
.hero-badges{display:flex;flex-wrap:wrap;gap:10px;margin-top:20px}
.hero-badge{display:inline-flex;align-items:center;gap:8px;padding:7px 12px;border-radius:999px;background:rgba(255,255,255,.14);border:1px solid rgba(255,255,255,.22);font-size:.85rem;font-weight:600}
.hero-badge .icon{width:16px;height:16px}
.hero::after{content:"";position:absolute;right:-60px;bottom:-80px;width:280px;height:280px;border-radius:50%;border:40px solid rgba(255,255,255,.07)}
.section-head{display:flex;align-items:baseline;justify-content:space-between;gap:12px;margin-bottom:16px}
.product-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(min(260px,100%),1fr));gap:20px}
.product-card{display:flex;flex-direction:column;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;box-shadow:var(--shadow-sm);transition:box-shadow .2s,border-color .2s}
.product-card:hover{box-shadow:var(--shadow);border-color:var(--border-strong)}
.product-card .body{display:flex;flex-direction:column;gap:6px;padding:16px 18px 18px;flex:1}
.product-card h2{font-size:1.05rem}
.product-card h2 a{color:var(--text)}
.product-card .desc{color:var(--muted);font-size:.92rem;flex:1}
.product-card .price{font-size:1.25rem;font-weight:750;margin:6px 0 10px}
.thumb{--hue:170;position:relative;display:grid;place-items:center;aspect-ratio:4/3;background:radial-gradient(80% 80% at 30% 20%,hsl(var(--hue) 70% 92%),transparent 70%),linear-gradient(135deg,hsl(var(--hue) 55% 86%),hsl(calc(var(--hue) + 40) 55% 78%));color:hsl(var(--hue) 45% 28%);text-decoration:none}
@media (prefers-color-scheme:dark){.thumb{background:radial-gradient(80% 80% at 30% 20%,hsl(var(--hue) 40% 26%),transparent 70%),linear-gradient(135deg,hsl(var(--hue) 35% 18%),hsl(calc(var(--hue) + 40) 35% 14%));color:hsl(var(--hue) 60% 78%)}}
.thumb:hover{text-decoration:none}
.thumb-icon{width:38%;height:38%;max-width:96px;opacity:.85;stroke-width:1.25}
.thumb-code{position:absolute;left:12px;top:12px;padding:3px 9px;border-radius:999px;background:color-mix(in srgb,var(--surface) 80%,transparent);font-size:.75rem;font-weight:700;color:var(--text)}
.thumb.sm{width:72px;height:72px;aspect-ratio:auto;border-radius:12px;flex:none}
.thumb.sm .thumb-code{display:none}
.thumb.sm .thumb-icon{width:32px;height:32px}
.thumb.lg{border-radius:var(--radius);aspect-ratio:1/1}
.add-form{display:flex;gap:10px;align-items:flex-end}
.add-form .field{margin:0}
.add-form .btn{flex:1}
.product-card .add-form{flex-wrap:wrap}
.product-card .add-form .field{flex-direction:row;align-items:center;gap:10px;flex:1 1 100%}
.product-card .add-form .btn{flex:1 1 100%}
.detail-info>.badge{align-self:flex-start}
.field{display:flex;flex-direction:column;gap:6px;margin:0 0 16px;font-size:.88rem;font-weight:650;color:var(--text)}
input{font:inherit;font-size:1rem;color:var(--text);background:var(--surface);min-height:44px;padding:10px 12px;border:1px solid var(--border-strong);border-radius:var(--radius-sm);max-width:100%;transition:border-color .18s,box-shadow .18s}
input:hover{border-color:var(--muted)}
input:focus{outline:none;border-color:var(--brand);box-shadow:0 0 0 3px color-mix(in srgb,var(--brand) 22%,transparent)}
input[type=number]{width:84px;text-align:center;font-variant-numeric:tabular-nums}
.detail{display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1fr);gap:clamp(20px,4vw,48px);align-items:start}
.detail-info{display:flex;flex-direction:column;gap:16px}
.detail-info .price{font-size:2rem;font-weight:800}
.detail-info .desc{color:var(--muted);font-size:1.02rem}
.buy-box{padding:20px;border:1px solid var(--border);border-radius:var(--radius);background:var(--surface)}
.buy-box .add-form .btn{min-height:48px}
.perks{display:grid;gap:12px;margin:0;padding:0;list-style:none}
.perks li{display:flex;gap:12px;align-items:flex-start;font-size:.92rem;color:var(--muted)}
.perks .icon{color:var(--brand);margin-top:1px}
.perks strong{display:block;color:var(--text);font-size:.95rem}
.layout-2{display:grid;grid-template-columns:minmax(0,1fr) 360px;gap:24px;align-items:start}
.summary{position:sticky;top:92px}
.summary dl{display:grid;grid-template-columns:1fr auto;gap:10px 0;margin:0 0 18px}
.summary dt{color:var(--muted)}
.summary dd{margin:0;padding-left:16px;text-align:right;font-weight:600}
.summary .total{padding-top:14px;border-top:1px dashed var(--border-strong);font-size:1.05rem;color:var(--text);font-weight:700}
.summary .total+dd{padding-top:14px;border-top:1px dashed var(--border-strong);font-size:1.3rem;font-weight:800}
.summary .fine{display:flex;gap:8px;margin-top:14px}
.summary .fine .icon{width:16px;height:16px;margin-top:2px}
.line-items{list-style:none;margin:0;padding:0}
.line-item{display:grid;grid-template-columns:72px minmax(0,1fr) auto auto auto;gap:16px;align-items:center;padding:16px 0;border-top:1px solid var(--border)}
.line-item:first-child{border-top:0;padding-top:0}
.line-item:last-child{padding-bottom:0}
.li-name{font-weight:650;color:var(--text)}
.li-unit{font-size:.86rem;color:var(--muted)}
.li-total{font-weight:750;min-width:120px;text-align:right}
.qty-form{display:flex;align-items:center;gap:6px}
.qty-form input{width:72px;min-height:38px;padding:6px 8px}
.empty{display:flex;flex-direction:column;align-items:center;text-align:center;gap:12px;padding:56px 24px}
.empty-icon{display:grid;place-items:center;width:72px;height:72px;border-radius:50%;background:var(--brand-soft);color:var(--brand);margin-bottom:6px}
.empty-icon .icon{width:32px;height:32px}
.empty.error .empty-icon{background:var(--bad-soft);color:var(--bad)}
.empty p{color:var(--muted);max-width:46ch}
.empty .actions{display:flex;flex-wrap:wrap;justify-content:center;gap:10px;margin-top:10px}
.alert{display:flex;gap:12px;padding:14px 16px;border-radius:var(--radius-sm);background:var(--info-soft);color:var(--text);font-size:.92rem}
.alert .icon{color:var(--info);margin-top:1px}
.alert.warn{background:var(--warn-soft)}.alert.warn .icon{color:var(--warn)}
.alert.bad{background:var(--bad-soft)}.alert.bad .icon{color:var(--bad)}
.badge{display:inline-flex;align-items:center;gap:7px;padding:4px 10px;border-radius:999px;font-size:.8rem;font-weight:700;background:var(--info-soft);color:var(--info);white-space:nowrap}
.badge .dot{width:7px;height:7px;border-radius:50%;background:currentColor}
.badge.ok{background:var(--ok-soft);color:var(--ok)}
.badge.wait{background:var(--warn-soft);color:var(--warn)}
.badge.bad{background:var(--bad-soft);color:var(--bad)}
.steps{display:flex;gap:8px;list-style:none;margin:0 0 24px;padding:0;counter-reset:s;flex-wrap:wrap}
.steps li{display:flex;align-items:center;gap:8px;font-size:.9rem;font-weight:650;color:var(--muted)}
.steps li+li::before{content:"";width:28px;height:2px;background:var(--border-strong);border-radius:2px}
.steps .n{display:grid;place-items:center;width:28px;height:28px;border-radius:50%;border:2px solid var(--border-strong);font-size:.8rem}
.steps .done .n{background:var(--brand);border-color:var(--brand);color:var(--on-brand)}
.steps .done .n .icon{width:14px;height:14px;stroke-width:3}
.steps .current{color:var(--text)}.steps .current .n{border-color:var(--brand);color:var(--brand)}
.pay-option{display:flex;gap:14px;align-items:center;padding:16px;border:2px solid var(--brand);border-radius:var(--radius-sm);background:var(--brand-soft)}
.pay-option .icon{color:var(--brand);width:24px;height:24px}
.pay-option .check{margin-left:auto;display:grid;place-items:center;width:24px;height:24px;border-radius:50%;background:var(--brand);color:var(--on-brand)}
.pay-option .check .icon{width:14px;height:14px;color:inherit;stroke-width:3}
.timeline{display:grid;grid-template-columns:repeat(3,1fr);gap:0;list-style:none;margin:8px 0 0;padding:0}
.timeline li{position:relative;display:flex;flex-direction:column;align-items:center;text-align:center;gap:8px;font-size:.88rem;font-weight:650;color:var(--muted)}
.timeline li::before{content:"";position:absolute;top:17px;left:-50%;width:100%;height:3px;background:var(--border)}
.timeline li:first-child::before{display:none}
.timeline .n{position:relative;z-index:1;display:grid;place-items:center;width:36px;height:36px;border-radius:50%;background:var(--surface);border:2px solid var(--border-strong)}
.timeline .n .icon{width:18px;height:18px}
.timeline .done{color:var(--text)}.timeline .done::before{background:var(--brand)}
.timeline .done .n{background:var(--brand);border-color:var(--brand);color:var(--on-brand)}
.timeline .failed{color:var(--bad)}.timeline .failed .n{border-color:var(--bad);color:var(--bad);background:var(--bad-soft)}
.order-list{list-style:none;margin:0;padding:0}
.order-row{display:grid;grid-template-columns:44px minmax(0,1fr) auto auto 20px;gap:16px;align-items:center;padding:16px 8px;border-top:1px solid var(--border);color:var(--text);border-radius:10px;transition:background .18s}
.order-list li:first-child .order-row{border-top:0}
.order-row:hover{background:var(--surface-2);text-decoration:none}
.order-row .ic{display:grid;place-items:center;width:44px;height:44px;border-radius:12px;background:var(--brand-soft);color:var(--brand)}
.order-row .id{font-weight:700}
.order-row .meta{font-size:.86rem;color:var(--muted)}
.order-row .chev{color:var(--muted);width:18px;height:18px}
.stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(min(240px,100%),1fr));gap:16px;margin:0}
.stat{display:flex;flex-direction:column;gap:8px;padding:22px;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);box-shadow:var(--shadow-sm)}
.stat dt{display:flex;align-items:center;gap:8px;color:var(--muted);font-size:.88rem;font-weight:650}
.stat dt .icon{color:var(--brand);width:18px;height:18px}
.stat dd{margin:0;font-size:1.75rem;font-weight:800;letter-spacing:-.02em}
.stat dd.text{font-size:1rem;font-weight:650}
.services{list-style:none;margin:0;padding:0;display:grid;gap:10px}
.services li{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:14px 16px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--surface)}
.services .name{display:flex;align-items:center;gap:10px;font-weight:650;font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-size:.92rem}
.auth{display:grid;grid-template-columns:1fr 1fr;max-width:960px;margin:12px auto 0;background:var(--surface);border:1px solid var(--border);border-radius:20px;overflow:hidden;box-shadow:var(--shadow)}
.auth-aside{display:flex;flex-direction:column;justify-content:space-between;gap:28px;padding:40px;background:radial-gradient(120% 120% at 100% 0%,#14b8a6 0%,transparent 55%),linear-gradient(160deg,#115e59,#0f766e);color:#fff}
.auth-aside h2{font-size:1.6rem;color:#fff;max-width:16ch}
.auth-aside p{color:rgba(255,255,255,.85);margin-top:10px}
.auth-aside .perks li{color:rgba(255,255,255,.85)}.auth-aside .perks strong{color:#fff}.auth-aside .perks .icon{color:#99f6e4}
.auth-form{padding:40px}
.auth-form h1{font-size:1.7rem}
.auth-form .lead{color:var(--muted);margin:8px 0 24px}
.auth-form input{width:100%}
.site-footer{border-top:1px solid var(--border);background:var(--surface);padding:40px 0 28px;font-size:.92rem}
.footer-grid{display:grid;grid-template-columns:1.6fr repeat(3,1fr);gap:28px}
.footer-grid h3{font-size:.8rem;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);margin-bottom:12px}
.footer-grid ul{list-style:none;margin:0;padding:0;display:grid;gap:8px}
.footer-grid a{color:var(--text)}
.footer-grid .about{color:var(--muted);margin-top:12px;max-width:36ch}
.footer-bottom{display:flex;flex-wrap:wrap;justify-content:space-between;gap:8px;margin-top:32px;padding-top:20px;border-top:1px solid var(--border);color:var(--muted);font-size:.85rem}
.row-actions{display:flex;flex-wrap:wrap;gap:10px;margin-top:20px}
.kv{display:grid;grid-template-columns:auto 1fr;gap:8px 20px;margin:0;font-size:.92rem}
.kv dt{color:var(--muted)}.kv dd{margin:0;font-weight:600}
@media (max-width:960px){.layout-2{grid-template-columns:1fr}.summary{position:static}.footer-grid{grid-template-columns:1fr 1fr}.auth{grid-template-columns:1fr}.auth-aside{padding:28px}}
@media (max-width:760px){
.header-row{flex-wrap:wrap;gap:10px 12px;min-height:0;padding:10px 0}
.main-nav{order:3;flex-basis:100%;overflow-x:auto;margin:0 -4px;padding-bottom:2px;scrollbar-width:none}
.main-nav::-webkit-scrollbar{display:none}
.nav-link{padding:8px 12px;font-size:.9rem}
.account-name,.hide-sm{position:absolute;width:1px;height:1px;overflow:hidden;clip-path:inset(50%);white-space:nowrap}
.header-actions .btn-sm{min-width:44px;padding:8px 10px}
.topbar{font-size:.75rem}
.detail{grid-template-columns:1fr}
.thumb.lg{aspect-ratio:16/10}
.line-item{grid-template-columns:64px minmax(0,1fr) auto;grid-template-areas:"t n n" "t q r" "t s s";gap:8px 12px}
.line-item .thumb{grid-area:t;width:64px;height:64px;align-self:start}
.line-item .li-main{grid-area:n}
.line-item .qty-form{grid-area:q}
.line-item .remove{grid-area:r;justify-self:end}
.line-item .li-total{grid-area:s;text-align:left;min-width:0}
.order-row{grid-template-columns:44px minmax(0,1fr) auto;grid-template-areas:"i a s" "i b b"}
.order-row .ic{grid-area:i}.order-row .main{grid-area:a}.order-row .status{grid-area:s}.order-row .amount{grid-area:b;font-size:.92rem}.order-row .chev{display:none}
}
@media (max-width:520px){.container{padding:0 16px}.panel{padding:18px}.page{padding:20px 0 48px}.hero{padding:28px 20px;border-radius:16px}.footer-grid{grid-template-columns:1fr}.auth-form{padding:24px}.add-form{flex-wrap:wrap}.timeline{font-size:.8rem}.steps li+li::before{width:14px}}
@media (prefers-reduced-motion:reduce){*{transition:none!important;scroll-behavior:auto!important}}
CSS;

function page(string $title,string $content,int $status=200,array $options=[]): never {
    http_response_code($status);header('Content-Type: text/html; charset=utf-8');
    $current=(string)parse_url($_SERVER['REQUEST_URI']??'/',PHP_URL_PATH);
    $signed=isset($_SESSION['buyer_id']);
    $account=$signed
        ?'<span class="account"><span class="avatar">'.icon('user').'</span><span class="account-name">Akun saya</span></span><form class="inline" method="post" action="/logout">'.csrf_field().'<button class="btn btn-ghost btn-sm">'.icon('logout').'<span class="hide-sm">Keluar</span></button></form>'
        :'<a class="btn btn-primary btn-sm" href="/login">'.icon('user').'<span>Masuk</span></a>';
    $head='';
    if(empty($options['custom_head'])){
        if(!empty($options['crumbs'])){
            $parts=[];foreach($options['crumbs'] as [$label,$href])$parts[]=$href===null?'<span aria-current="page">'.h($label).'</span>':'<a href="'.h($href).'">'.h($label).'</a>';
            $head.='<nav class="crumbs" aria-label="Breadcrumb">'.implode(icon('chevron'),$parts).'</nav>';
        }
        $head.='<div class="page-head"><div><h1>'.h($title).'</h1>'.(isset($options['lead'])?'<p class="lead">'.h($options['lead']).'</p>':'').'</div>'.($options['actions']??'').'</div>';
    }
    echo '<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="color-scheme" content="light dark"><meta name="theme-color" content="#0f766e"><title>'.h($title).' · NusaMart</title><style>'.STYLES.'</style></head><body>'
        .'<a class="sr-only" href="#konten">Lewati ke konten</a>'
        .'<div class="topbar"><div class="container">'.icon('info').'<span>Toko latihan · IDR_DUMMY · Tanpa pembayaran atau pengiriman nyata</span></div></div>'
        .'<header class="site-header"><div class="container header-row"><a class="brand" href="/products"><span class="brand-mark">'.icon('bag').'</span><span>Nusa<b>Mart</b></span></a>'
        .'<nav class="main-nav" aria-label="Navigasi utama">'.nav_link('/products','Katalog','grid',$current).nav_link('/orders','Pesanan','receipt',$current).nav_link('/reports','Laporan','chart',$current).'</nav>'
        .'<div class="header-actions"><a class="btn btn-ghost btn-sm cart-link" href="/cart"'.($current==='/cart'?' aria-current="page"':'').'>'.icon('cart').'<span class="hide-sm">Keranjang</span></a>'.$account.'</div></div></header>'
        .'<main class="page" id="konten"><div class="container">'.$head.$content.'</div></main>'
        .'<footer class="site-footer"><div class="container"><div class="footer-grid">'
        .'<div><a class="brand" href="/products"><span class="brand-mark">'.icon('bag').'</span><span>Nusa<b>Mart</b></span></a><p class="about">Marketplace latihan untuk simulasi respons kerentanan. Seluruh data, harga dan pembayaran bersifat dummy.</p></div>'
        .'<div><h3>Belanja</h3><ul><li><a href="/products">Katalog</a></li><li><a href="/cart">Keranjang</a></li><li><a href="/checkout">Checkout</a></li></ul></div>'
        .'<div><h3>Akun</h3><ul><li><a href="/orders">Pesanan saya</a></li><li><a href="/reports">Laporan pembeli</a></li>'.($signed?'':'<li><a href="/login">Masuk</a></li>').'</ul></div>'
        .'<div><h3>Sistem</h3><ul><li><a href="/lab/status">Status lab</a></li></ul></div>'
        .'</div><div class="footer-bottom"><span>© 2026 NusaMart · Data dan pembayaran dummy</span><span>Lingkungan latihan keamanan aplikasi</span></div></div></footer>'
        .'</body></html>';
    exit;
}
function empty_state(string $iconName,string $title,string $message,string $actions,bool $error=false): string {
    return '<section class="panel empty'.($error?' error':'').'"><div class="empty-icon">'.icon($iconName).'</div><h2>'.h($title).'</h2><p>'.h($message).'</p><div class="actions">'.$actions.'</div></section>';
}
function add_form(string $id,bool $compact=true): string {
    return '<form class="add-form" method="post" action="/cart/items">'.csrf_field().'<input type="hidden" name="product_id" value="'.h($id).'"><label class="field">Jumlah <input name="quantity" type="number" value="1" min="1" max="20" required></label><button class="btn '.($compact?'btn-primary':'btn-accent btn-lg').'">'.icon('cart').'<span>Tambah ke keranjang</span></button></form>';
}
function catalog_html(array $products): string {
    $html='<section class="hero"><h1>Katalog produk</h1><p>Temukan produk pilihan dan selesaikan checkout dalam hitungan detik. Semua transaksi memakai saldo IDR_DUMMY.</p><div class="hero-badges"><span class="hero-badge">'.icon('shield').'Checkout terlindungi CSRF</span><span class="hero-badge">'.icon('wallet').'Pembayaran dummy</span><span class="hero-badge">'.icon('truck').'Tanpa pengiriman nyata</span></div></section>';
    $html.='<div class="section-head"><h2>Semua produk</h2><span class="muted">'.h(count($products)).' produk</span></div>';
    if(!$products)return $html.empty_state('package','Belum ada produk','Katalog sedang kosong. Coba lagi nanti.','');
    $html.='<div class="product-grid">';
    foreach($products as $product){
        $href='/products/'.rawurlencode($product['id']);
        $html.='<article class="product-card">'.product_thumb((string)$product['id'],'',$href).'<div class="body"><h2><a href="'.h($href).'">'.h($product['name']).'</a></h2><p class="desc">'.h($product['description']).'</p><p class="price">'.money($product['price']).'</p>'.add_form((string)$product['id']).'</div></article>';
    }
    return $html.'</div>';
}
function product_html(array $product): string {
    return '<div class="detail">'.product_thumb((string)$product['id'],'lg').'<div class="detail-info"><span class="badge ok"><span class="dot"></span>Tersedia</span><p class="price">'.money($product['price']).'</p><p class="desc">'.h($product['description']).'</p>'
        .'<div class="buy-box">'.add_form((string)$product['id'],false).'<p class="fine" style="margin-top:10px">Maksimal 20 per produk dalam satu keranjang.</p></div>'
        .'<ul class="perks"><li>'.icon('shield').'<div><strong>Transaksi aman</strong>Sesi dan form dilindungi token CSRF.</div></li><li>'.icon('wallet').'<div><strong>Pembayaran dummy</strong>Tidak ada uang yang ditagih.</div></li><li>'.icon('truck').'<div><strong>Tanpa pengiriman</strong>Pesanan hanya diproses secara simulasi.</div></li></ul></div></div>';
}
function cart_html(array $cart): string {
    if(!$cart['items'])return empty_state('cart','Keranjang kosong','Belum ada produk di keranjang Anda. Jelajahi katalog dan tambahkan produk favorit.','<a class="btn btn-primary" href="/products">'.icon('grid').'Pilih produk</a>');
    $count=0;$rows='';
    foreach($cart['items'] as $item){
        $count+=$item['quantity'];$field='<input type="hidden" name="product_id" value="'.h($item['product_id']).'">';
        $rows.='<li class="line-item">'.product_thumb((string)$item['product_id'],'sm').'<div class="li-main"><a class="li-name" href="/products/'.h(rawurlencode((string)$item['product_id'])).'">'.h($item['name']).'</a><div class="li-unit">'.money($item['price']).' / pcs</div></div>'
            .'<form class="qty-form" method="post" action="/cart/items/update">'.csrf_field().$field.'<input aria-label="Jumlah '.h($item['name']).'" name="quantity" type="number" min="1" max="20" value="'.h($item['quantity']).'"><button class="btn btn-sm">Ubah</button></form>'
            .'<div class="li-total">'.money($item['total']).'</div>'
            .'<form class="remove" method="post" action="/cart/items/remove">'.csrf_field().$field.'<button class="btn btn-ghost btn-sm btn-danger">'.icon('trash').'<span>Hapus</span></button></form></li>';
    }
    return '<div class="layout-2"><section class="panel"><div class="panel-title">'.icon('bag').'<h2>Produk di keranjang</h2></div><ul class="line-items">'.$rows.'</ul></section>'
        .'<aside class="panel summary" aria-label="Ringkasan belanja"><h2 style="margin-bottom:16px">Ringkasan belanja</h2><dl><dt>Subtotal ('.h($count).' barang)</dt><dd>'.money($cart['total']).'</dd><dt>Ongkos kirim</dt><dd>Tidak ada</dd><dt class="total">Total</dt><dd>'.money($cart['total']).'</dd></dl>'
        .'<a class="btn btn-accent btn-block btn-lg" href="/checkout">Lanjut checkout dummy'.icon('arrow').'</a><p class="fine">'.icon('lock').'<span>Harga dihitung ulang oleh server saat checkout.</span></p></aside></div>';
}
function steps_html(int $current): string {
    $html='<ol class="steps" aria-label="Tahap pembelian">';
    foreach(['Keranjang','Checkout','Selesai'] as $index=>$label){
        $state=$index<$current?'done':($index===$current?'current':'');
        $html.='<li class="'.$state.'"'.($index===$current?' aria-current="step"':'').'><span class="n">'.($index<$current?icon('check'):h($index+1)).'</span>'.h($label).'</li>';
    }
    return $html.'</ol>';
}
function checkout_html(array $cart,string $key): string {
    $rows='';foreach($cart['items'] as $item)$rows.='<li class="line-item" style="grid-template-columns:72px minmax(0,1fr) auto">'.product_thumb((string)$item['product_id'],'sm').'<div class="li-main"><span class="li-name">'.h($item['name']).'</span><div class="li-unit">'.h($item['quantity']).' × '.money($item['price']).'</div></div><div class="li-total">'.money($item['total']).'</div></li>';
    return steps_html(1).'<div class="layout-2"><div><section class="panel"><div class="panel-title">'.icon('bag').'<h2>Barang pesanan</h2></div><ul class="line-items">'.$rows.'</ul></section>'
        .'<section class="panel"><div class="panel-title">'.icon('wallet').'<h2>Metode pembayaran</h2></div><div class="pay-option">'.icon('wallet').'<div><strong>Saldo IDR_DUMMY</strong><p class="fine">Konfirmasi ini hanya pembayaran dummy. Tidak ada uang yang ditagih.</p></div><span class="check">'.icon('check').'</span></div></section></div>'
        .'<aside class="panel summary" aria-label="Ringkasan pembayaran"><h2 style="margin-bottom:16px">Ringkasan pembayaran</h2><dl><dt>Total harga</dt><dd>'.money($cart['total']).'</dd><dt>Ongkos kirim</dt><dd>Tidak ada</dd><dt class="total">Total</dt><dd>'.money($cart['total']).'</dd></dl>'
        .'<form method="post">'.csrf_field().'<input type="hidden" name="cart_id" value="'.h($cart['cart_id']).'"><input type="hidden" name="cart_version" value="'.h($cart['cart_version']).'"><input type="hidden" name="checkout_key" value="'.h($key).'"><button class="btn btn-accent btn-block btn-lg">'.icon('lock').'<span>Konfirmasi pembayaran dummy</span></button></form>'
        .'<p class="fine">'.icon('shield').'<span>Permintaan yang sama aman diulang; pesanan tidak akan tercatat ganda.</span></p><p style="margin-top:12px"><a href="/cart">Kembali ke keranjang</a></p></aside></div>';
}
function order_html(array $order): string {
    $status=(string)$order['status'];
    $reached=['queued'=>0,'processing'=>1,'completed'=>2,'failed'=>1][$status]??0;
    $timeline='<ol class="timeline" aria-label="Progres pesanan">';
    foreach([['Diterima','receipt'],['Diproses','activity'],[$status==='failed'?'Gagal':'Selesai',$status==='failed'?'alert':'check']] as $index=>[$label,$iconName]){
        $class=$status==='failed'&&$index===2?'failed':($index<=$reached?'done':'');
        $timeline.='<li class="'.$class.'"><span class="n">'.icon($iconName).'</span>'.h($label).'</li>';
    }
    $timeline.='</ol>';
    $rows='';foreach($order['items'] as $item)$rows.='<li class="line-item" style="grid-template-columns:72px minmax(0,1fr) auto">'.product_thumb((string)$item['product_id'],'sm').'<div class="li-main"><span class="li-name">'.h($item['name']).'</span><div class="li-unit">'.h($item['quantity']).' × '.money($item['price']).'</div></div><div class="li-total">'.money($item['total']).'</div></li>';
    $job=$order['job']??null;
    $failure=$status==='failed'?'<div class="alert bad" style="margin-top:16px">'.icon('alert').'<span>Pemrosesan gagal setelah '.h($job['attempts']??0).' percobaan'.(!empty($job['error_code'])?' ('.h($job['error_code']).')':'').'.</span></div>':'';
    $pending=in_array($status,['queued','processing'],true)?'<div class="alert" style="margin-top:16px">'.icon('clock').'<span>Pemrosesan berjalan di background. Muat ulang untuk status terbaru.</span></div>':'';
    return '<div class="layout-2"><div><section class="panel"><div class="panel-title" style="justify-content:space-between;flex-wrap:wrap"><h2>Status pesanan</h2>'.status_badge($status).'</div>'.$timeline.$pending.$failure.'</section>'
        .'<section class="panel"><div class="panel-title">'.icon('bag').'<h2>Barang dibeli</h2></div><ul class="line-items">'.$rows.'</ul></section></div>'
        .'<aside class="panel summary" aria-label="Rincian pesanan"><h2 style="margin-bottom:16px">Rincian</h2><dl><dt>Nomor pesanan</dt><dd>#'.h($order['id']).'</dd><dt>Dibuat</dt><dd>'.format_time($order['created_at']).'</dd><dt>Pembayaran</dt><dd>Dummy</dd><dt class="total">Total</dt><dd>'.money($order['total']).'</dd></dl>'
        .'<div class="row-actions" style="margin-top:0"><a class="btn btn-sm" href="/orders/'.h($order['id']).'">'.icon('refresh').'Perbarui status</a><a class="btn btn-sm" href="/reports">'.icon('chart').'Lihat laporan</a></div></aside></div>';
}
function orders_html(array $orders): string {
    if(!$orders)return empty_state('receipt','Belum ada pesanan','Pesanan yang Anda buat akan muncul di sini beserta status pemrosesannya.','<a class="btn btn-primary" href="/products">'.icon('grid').'Mulai belanja</a>');
    $html='<section class="panel"><ul class="order-list">';
    foreach($orders as $order)$html.='<li><a class="order-row" href="/orders/'.h($order['id']).'"><span class="ic">'.icon('receipt').'</span><span class="main"><span class="id">Pesanan #'.h($order['id']).'</span><br><span class="meta">'.format_time($order['created_at']).'</span></span><span class="amount">'.money($order['total']).'</span><span class="status">'.status_badge((string)$order['status']).'</span>'.icon('chevron','icon chev').'</a></li>';
    return $html.'</ul></section>';
}
function report_html(array $report): string {
    $updated=$report['updated_at']?'<dd class="text">'.format_time($report['updated_at']).'</dd>':'<dd class="text">Belum ada snapshot. Menunggu reporting worker.</dd>';
    return '<dl class="stats"><div class="stat"><dt>'.icon('receipt').'Pesanan selesai:</dt><dd>'.h($report['completed_count']).'</dd></div><div class="stat"><dt>'.icon('wallet').'Total selesai:</dt><dd>'.money($report['completed_total']).'</dd></div><div class="stat"><dt>'.icon('clock').'Pembaruan terakhir:</dt>'.$updated.'</div></dl>'
        .'<div class="alert" style="margin-top:20px">'.icon('info').'<span>Ringkasan diproses di background dan dapat tertinggal dari status pesanan.</span></div>'
        .'<div class="row-actions"><a class="btn" href="/reports">'.icon('refresh').'Perbarui laporan</a><a class="btn btn-ghost" href="/orders">Lihat pesanan</a></div>';
}
function status_html(array $services,bool $ok): string {
    $html='<div class="alert '.($ok?'':'bad').'" style="margin-bottom:16px">'.icon($ok?'check':'alert').'<span>'.($ok?'Semua layanan beroperasi normal.':'Sebagian layanan tidak tersedia.').' Empat layanan HTTP yang dipantau. Status diperiksa saat halaman dimuat.</span></div><ul class="services">';
    foreach($services as $name=>$healthy)$html.='<li><span class="name">'.icon('activity').h($name).'</span><span class="badge '.($healthy?'ok':'bad').'"><span class="dot"></span>'.($healthy?'Sehat':'Tidak tersedia').'</span></li>';
    return $html.'</ul><div class="row-actions"><a class="btn" href="/lab/status">'.icon('refresh').'Perbarui status</a></div>';
}
function login_html(): string {
    return '<div class="auth"><div class="auth-aside"><div><h2>Belanja cepat, aman, tanpa ribet.</h2><p>Masuk untuk mengelola keranjang, checkout dan memantau pesanan Anda.</p></div><ul class="perks"><li>'.icon('shield').'<div><strong>Sesi terlindungi</strong>Cookie HttpOnly dan token CSRF di setiap form.</div></li><li>'.icon('wallet').'<div><strong>Pembayaran dummy</strong>Tidak ada uang sungguhan yang ditagih.</div></li><li>'.icon('activity').'<div><strong>Pantau pesanan</strong>Status diproses otomatis oleh worker.</div></li></ul></div>'
        .'<section class="auth-form"><h1>Masuk pembeli</h1><p class="lead">Gunakan akun dummy dari docs/AKUN-LAB.md.</p><form method="post">'.csrf_field().'<label class="field">Nama pembeli <input name="username" autocomplete="username" required></label><label class="field">Kata sandi <input name="password" type="password" autocomplete="current-password" required></label><button class="btn btn-primary btn-block btn-lg">'.icon('lock').'<span>Masuk</span></button></form><p class="fine" style="margin-top:16px">Belum punya akun? Lihat docs/AKUN-LAB.md untuk kredensial latihan.</p></section></div>';
}
