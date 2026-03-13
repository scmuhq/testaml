<?php
/**
 * update_google_ips.php
 * Récupère TOUTES les plages IP Google depuis GitHub (IPv4 + IPv6, tous fichiers)
 * et met à jour prevents/google_ips.php automatiquement.
 *
 * Sources utilisées :
 *   - ipv4.txt              (toutes les IPv4 Google agrégées)
 *   - ipv6.txt              (toutes les IPv6 Google agrégées)
 *   - googlebot.json        (IPs Googlebot spécifiques)
 *   - special-crawlers.json (autres crawlers Google)
 *   - user-triggered-fetchers.json (fetchers déclenchés par l'utilisateur)
 *
 * Tâche planifiée Windows (toutes les heures) :
 *   schtasks /create /tn "UpdateGoogleIPs" /tr "C:\php-8.5.1\php.exe C:\...\update_google_ips.php" /sc hourly /mo 1 /f
 *
 * Cron Linux (toutes les heures) :
 *   0 * * * * php /var/www/html/update_google_ips.php
 */

$sources = [
    'ipv4'     => 'https://raw.githubusercontent.com/aminvakil/google-ip-list/main/ipv4.txt',
    'ipv6'     => 'https://raw.githubusercontent.com/aminvakil/google-ip-list/main/ipv6.txt',
    'googlebot'=> 'https://raw.githubusercontent.com/aminvakil/google-ip-list/main/googlebot.json',
    'special'  => 'https://raw.githubusercontent.com/aminvakil/google-ip-list/main/special-crawlers.json',
    'fetchers' => 'https://raw.githubusercontent.com/aminvakil/google-ip-list/main/user-triggered-fetchers.json',
];

$ctx = stream_context_create(['http' => [
    'timeout'    => 20,
    'user_agent' => 'PHP-GoogleIPUpdater/1.0',
]]);

function fetch_url($url, $ctx) {
    $r = @file_get_contents($url, false, $ctx);
    if ($r === false) { echo "[WARN] Impossible de récupérer : $url\n"; }
    return $r ?: '';
}

// Pattern IPv4 : compresse en préfixe /24 → ^X.Y.Z.
function ipv4_pattern($cidr) {
    $ip = explode('/', trim($cidr))[0];
    $p  = explode('.', $ip);
    if (count($p) < 3) return null;
    return '^' . $p[0] . '.' . $p[1] . '.' . $p[2] . '.';
}

// Pattern IPv6 : compresse en préfixe /48 → ^X:Y:Z:
// Cela couvre tous les /64 partageant le même /48 (3 premiers groupes)
function ipv6_pattern($cidr) {
    $ip     = explode('/', trim($cidr))[0];
    $ip     = rtrim($ip, ':');           // retire les :: finaux
    $groups = explode(':', $ip);
    if (count($groups) < 3) return null;
    return '^' . $groups[0] . ':' . $groups[1] . ':' . $groups[2] . ':';
}

$patterns = [];

// --- Traitement des fichiers TXT (listes complètes agrégées) ---
echo "[1/5] Traitement ipv4.txt...\n";
foreach (explode("\n", fetch_url($sources['ipv4'], $ctx)) as $line) {
    $p = ipv4_pattern($line);
    if ($p) $patterns[$p] = true;
}

echo "[2/5] Traitement ipv6.txt...\n";
foreach (explode("\n", fetch_url($sources['ipv6'], $ctx)) as $line) {
    $p = ipv6_pattern($line);
    if ($p) $patterns[$p] = true;
}

// --- Traitement des fichiers JSON (sous-ensembles, dédupliqués automatiquement) ---
$json_sources = [
    '[3/5] googlebot.json'              => $sources['googlebot'],
    '[4/5] special-crawlers.json'       => $sources['special'],
    '[5/5] user-triggered-fetchers.json'=> $sources['fetchers'],
];

foreach ($json_sources as $label => $url) {
    echo "$label...\n";
    $data = json_decode(fetch_url($url, $ctx), true);
    if (!isset($data['prefixes'])) continue;
    foreach ($data['prefixes'] as $entry) {
        if (isset($entry['ipv4Prefix'])) {
            $p = ipv4_pattern($entry['ipv4Prefix']);
        } elseif (isset($entry['ipv6Prefix'])) {
            $p = ipv6_pattern($entry['ipv6Prefix']);
        } else {
            continue;
        }
        if ($p) $patterns[$p] = true;
    }
}

$patterns = array_keys($patterns);
sort($patterns);

if (empty($patterns)) {
    die("[ERREUR] Aucun pattern généré — listes vides ou inaccessibles.\n");
}

// Séparation IPv4 / IPv6 pour le rapport
$ipv4_count = count(array_filter($patterns, fn($p) => strpos($p, ':') === false));
$ipv6_count = count($patterns) - $ipv4_count;

// Génération du fichier PHP
$arr  = implode('","', $patterns);
$dest = __DIR__ . '/prevents/google_ips.php';

$php  = "<?php\n";
$php .= "// AUTO-GÉNÉRÉ par update_google_ips.php — NE PAS MODIFIER MANUELLEMENT\n";
$php .= "// Sources : ipv4.txt, ipv6.txt, googlebot.json, special-crawlers.json, user-triggered-fetchers.json\n";
$php .= "// Repo    : https://github.com/aminvakil/google-ip-list\n";
$php .= "// Màj     : " . date('Y-m-d H:i:s') . " | IPv4: {$ipv4_count} | IPv6: {$ipv6_count}\n";
$php .= "\$google_ips = array(\"{$arr}\");\n";

if (file_put_contents($dest, $php) === false) {
    die("[ERREUR] Impossible d'écrire dans {$dest}.\n");
}

echo "[OK] {$ipv4_count} patterns IPv4 + {$ipv6_count} patterns IPv6 = " . count($patterns) . " total → {$dest}\n";
