<?php
/* config/mailer.php
Sends email through Gmail SMTP using an App Password via a raw socket (STARTTLS + AUTH LOGIN). 
All outgoing mail logs to storage/mail_log.txt for local testing and debugging failed sends. */

require_once __DIR__ . '/mail_config.php';

/**
 * Sends one HTML email. Returns true if it actually left the server,
 * false otherwise (check storage/mail_log.txt for why).
 */
function sendMail(string $toEmail, string $toName, string $subject, string $htmlBody): bool {
    $attempts = [];

    [$ok, $log] = smtpSend($toEmail, $toName, $subject, $htmlBody);
    $attempts[] = 'SMTP: ' . ($ok ? 'sent' : 'FAILED') . "\n$log";

    if ($ok) {
        logMail($toEmail, $subject, $htmlBody, $attempts, true);
        return true;
    }

    logMail($toEmail, $subject, $htmlBody, $attempts, false);
    return false;
}

/**
 * Minimal SMTP client: connect, STARTTLS, AUTH LOGIN, send one HTML email.
 * Returns [bool success, string transcriptLog] (credentials are never
 * written to the log).
 */
function smtpSend(string $toEmail, string $toName, string $subject, string $htmlBody): array {
    $host = SMTP_HOST;
    $port = SMTP_PORT;
    $user = SMTP_USER;
    $pass = SMTP_PASS;
    $from = MAIL_FROM;
    $fromName = MAIL_FROM_NAME;
    $log  = '';

    $read = function ($sock) use (&$log) {
        $data = '';
        while ($line = fgets($sock, 515)) {
            $data .= $line;
            if (isset($line[3]) && $line[3] === ' ') break;
        }
        $log .= $data;
        return $data;
    };
    $write = function ($sock, string $cmd) use (&$log) {
        $log .= (stripos($cmd, 'AUTH') === 0 ? "[credential omitted]\r\n" : "> $cmd\r\n");
        fwrite($sock, $cmd . "\r\n");
    };

    $errno = 0; $errstr = '';
    $sock = @stream_socket_client("tcp://$host:$port", $errno, $errstr, 10);
    if (!$sock) {
        return [false, "Could not connect to $host:$port — $errstr ($errno)"];
    }
    stream_set_timeout($sock, 10);

    $read($sock);
    $write($sock, "EHLO animoclaim.local");
    $read($sock);

    $write($sock, "STARTTLS");
    $resp = $read($sock);
    if (strpos($resp, '220') === false) {
        fclose($sock);
        return [false, "STARTTLS refused by server.\n$log"];
    }
    if (!@stream_socket_enable_crypto($sock, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
        fclose($sock);
        return [false, "TLS handshake failed.\n$log"];
    }

    $write($sock, "EHLO animoclaim.local");
    $read($sock);

    $write($sock, "AUTH LOGIN");
    $read($sock);
    $write($sock, base64_encode($user));
    $read($sock);
    $write($sock, base64_encode($pass));
    $authResp = $read($sock);
    if (strpos($authResp, '235') === false) {
        fclose($sock);
        return [false, "Authentication failed — check SMTP_USER/SMTP_PASS in config/mail_config.php.\n$log"];
    }

    $write($sock, "MAIL FROM:<$from>");
    $read($sock);
    $write($sock, "RCPT TO:<$toEmail>");
    $rcptResp = $read($sock);
    if (strpos($rcptResp, '250') === false) {
        fclose($sock);
        return [false, "Recipient rejected: $toEmail\n$log"];
    }
    $write($sock, "DATA");
    $read($sock);

    $headers  = "From: $fromName <$from>\r\n";
    $headers .= "To: " . ($toName ? "$toName <$toEmail>" : $toEmail) . "\r\n";
    $headers .= "Subject: $subject\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "Content-Transfer-Encoding: 8bit\r\n";
    $body = preg_replace('/\n\./', "\n..", $htmlBody);

    fwrite($sock, $headers . "\r\n" . $body . "\r\n.\r\n");
    $dataResp = $read($sock);
    $write($sock, "QUIT");
    fclose($sock);

    if (strpos($dataResp, '250') === false) {
        return [false, "Server rejected the message body.\n$log"];
    }
    return [true, $log];
}

function logMail(string $to, string $subject, string $body, array $attempts, bool $succeeded): void {
    $logDir = __DIR__ . '/../storage';
    if (!is_dir($logDir)) @mkdir($logDir, 0775, true);
    $status = $succeeded ? 'SENT' : 'NOT SENT — see attempts below';
    @file_put_contents(
        $logDir . '/mail_log.txt',
        '[' . date('Y-m-d H:i:s') . "] To: $to | Subject: $subject | $status\n"
      . implode("\n", $attempts) . "\n\n--- message body ---\n$body\n\n=====\n\n",
        FILE_APPEND
    );
}

/** Small HTML helper: one label/value row inside an email's detail table. */
function _emailRow(string $label, string $value): string {
    return '<tr><td style="padding:5px 14px 5px 0;color:#6b7a63;white-space:nowrap;font-size:13px">' . htmlspecialchars($label) . '</td>'
         . '<td style="padding:5px 0;font-weight:700;color:#163300;font-size:13px">' . $value . '</td></tr>';
}

/** Shared AnimoClaim email shell (green branding matching the app UI). */
function _emailWrapper(string $heading, string $intro, string $rowsHtml, string $footerNote = ''): string {
    return '<div style="font-family:Arial,Helvetica,sans-serif;max-width:520px;margin:0 auto;color:#163300">'
         . '<div style="background:#0e0f0c;border-radius:20px;padding:18px 22px;margin-bottom:18px">'
         . '<h1 style="color:#9fe870;margin:0;font-size:20px;letter-spacing:-0.5px">AnimoClaim</h1>'
         . '<p style="color:#9aa89a;margin:2px 0 0;font-size:11px;font-weight:700;letter-spacing:0.5px;text-transform:uppercase">DLSU Official Giveaway Portal</p>'
         . '</div>'
         . '<h2 style="margin:0 0 10px;font-size:17px;color:#163300">' . htmlspecialchars($heading) . '</h2>'
         . '<p style="font-size:14px;line-height:1.5">' . $intro . '</p>'
         . '<table style="border-collapse:collapse;width:100%;margin:16px 0;border-top:1px solid #e2f6d5;border-bottom:1px solid #e2f6d5">' . $rowsHtml . '</table>'
         . ($footerNote ? '<p style="color:#6b7a63;font-size:12px;line-height:1.5">' . $footerNote . '</p>' : '')
         . '<p style="color:#a3ada0;font-size:11px;margin-top:24px">This is an automated message from AnimoClaim. Please do not reply to this email.</p>'
         . '</div>';
}
