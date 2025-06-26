<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

function enviar_email_senha($email, $nome, $senhaTemporaria) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'mail.actecpg.com.br';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'senhas@actecpg.com.br';
        $mail->Password   = ')&m-0dhd&ay6';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;
        $mail->CharSet    = 'UTF-8'; // <- aqui está o ajuste

        $mail->setFrom('senhas@actecpg.com.br', 'Equipe GC');
        $mail->addAddress($email, $nome);

        $mail->isHTML(true);
        $mail->Subject = 'Senha temporária - Acesso ao sistema GC';
        $mail->Body    = "
            Olá <strong>$nome</strong>,<br><br>
            Sua conta foi criada com sucesso.<br>
            Sua senha temporária é: <strong>$senhaTemporaria</strong><br><br>
            Por favor, altere sua senha após o primeiro acesso.<br><br>
            Atenciosamente,<br>
            Equipe GC
        ";
        $mail->AltBody = "Olá $nome, sua senha temporária é: $senhaTemporaria. Altere sua senha após o primeiro acesso.";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Erro ao enviar e-mail: {$mail->ErrorInfo}");
        return false;
    }
}

function enviar_email_confirmacao_senha($email, $nome) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'mail.actecpg.com.br';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'senhas@actecpg.com.br';
        $mail->Password   = ')&m-0dhd&ay6';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;
        $mail->CharSet    = 'UTF-8'; // <- e aqui também

        $mail->setFrom('senhas@actecpg.com.br', 'Equipe GC');
        $mail->addAddress($email, $nome);

        $mail->isHTML(true);
        $mail->Subject = 'Confirmação de alteração de senha - Sistema GC';
        $mail->Body    = "
            Olá <strong>$nome</strong>,<br><br>
            Sua senha foi alterada com sucesso.<br><br>
            Se você não realizou essa alteração, por favor, contate a empresa imediatamente.<br><br>
            Atenciosamente,<br>
            Equipe GC
        ";
        $mail->AltBody = "Olá $nome, sua senha foi alterada com sucesso. Se não foi você, entre em contato com o suporte.";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Erro ao enviar e-mail de confirmação: {$mail->ErrorInfo}");
        return false;
    }
}
