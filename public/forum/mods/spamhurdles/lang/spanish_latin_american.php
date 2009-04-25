<?php
$PHORUM["DATA"]["LANG"]["mod_spamhurdles"] = array
(
    // Code CAPTCHA
    "CaptchaTitle" => "Prevenci�n de SPAM:",
    "CaptchaExplain" => "Porfavor introduce el c�digo que ves abajo en el espacio proporcionado. ",
    "CaptchaUnclearExplain" => "Si se te dificulta su lectura, adivinalo. Si introduces un c�digo err�neo, se crear� una nueva imagen.",
    "CaptchaSpoken" => "Escucha el c�digo.",
    "CaptchaFieldLabel" => "Introduce el c�digo: ",
    "CaptchaWrongCode" => "No proporcionaste el c�digo correcto, porfavor intentalo de nuevo.",

    // Mathematical CAPTCHA
    "MaptchaTitle" => "Prevenci�n de Spam:",
    "MaptchaExplain" => "Porfavor, soluciona el problema matem�tico en el campo proporcionado abajo. Esta es una forma de blocker robots que intentan postear autom�ticamente.",
    "MaptchaQuestion" => "Pregunta: cu�nto es {NUMBER1} mas {NUMBER2}?",
    "MaptchaSpoken" => "Escucha la pregunta.",
    "MaptchaFieldLabel" => "Respuesta: ",
    "MaptchaWrongAnswer" => "No proporcionaste la respuesta correcta. Pofavor, intenta de nuevo.",

    // Javascript CAPTCHA.
    "JavascriptCaptchaNoscript" => "[Porfavor, activa JavaScript para ver el c�digo]",

    // Generic message when a block was hit, but the user is still allowed
    // to post an automatically unapproved message.
    "PostingUnapproveError" => "Software Anti-Spam en este servidor ha detectado que tu mensaje podr�a ser spam. A�n puedes postear este mensaje, pero no se podr� visualizar en los foros hasta que un moderador lo apruebe. Puedes intentar someter de nuevo tu mensaje.",

    // Generic message when blocking a message. We do not want to
    // feed specific blocking reasons to those who are blocked, because
    // that info might be used to bypass the blocking reasons.
    "BlockError" => "Software Anti-Spam en este servidor ha detectado que tu mensaje podr�a se Spam, y por lo tanto ha sido bloqueado. Si tu mensaje no era spam, disculpa la inconveniencia. Si tus mensajes contin�an siendo bloqueados, porfavor contacta al administrador de este sitio para que te ayude. <br/><br/><b>Note</b>: Si tienes habilitado JavaScript en tu navegador o si tu navegador no lo respalda, esta podr�a ser la raz�n de que tus mensajes sean bloqueados. Alg�nas de las medidas Anti-Spam dependen de JavaScript.",
);
?>
