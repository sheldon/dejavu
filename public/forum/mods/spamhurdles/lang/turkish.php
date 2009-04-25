<?php
$PHORUM["DATA"]["LANG"]["mod_spamhurdles"] = array
(
    // Code CAPTCHA
    "CaptchaTitle" => "Spam korumas�:",
    "CaptchaExplain" => "L�tfen a�a��daki kodu giriniz. Bu koruma otomatik hesap a�an botlar i�in geli�tirilmi�tir.",
    "CaptchaUnclearExplain" => "E�er kodu okumakta zorlan�yorsan�z, sadece tahmin edin. E�er yanl�� kod girerseniz, yeniden girmeniz i�in yeni bir kod yarat�lacakt�r.",
    "CaptchaSpoken" => "Kodu konu�ma formunda dinle. (Not: Konu�ma formu ingilizcedir.)",
    "CaptchaFieldLabel" => "Kodu Gir: ",
    "CaptchaWrongCode" => "Kodu verildi�i gibi girmediniz. L�tfen tekrar deneyiniz.",

    // Mathematical CAPTCHA
    "MaptchaTitle" => "Spam korumas�:",
    "MaptchaExplain" => "Please, solve the mathematical question and enter the answer in the input field below. This is for blocking bots that try to post this form automatically.",
    "MaptchaQuestion" => "Question: how much is {NUMBER1} plus {NUMBER2}?",
    "MaptchaSpoken" => "Listen to this question in spoken form.",
    "MaptchaFieldLabel" => "Answer: ",
    "MaptchaWrongAnswer" => "You did not provide the correct answer for the spam prevention question. Please try again.",

    // Javascript CAPTCHA.
    "JavascriptCaptchaNoscript" => "[L�tfen taray�c�n�z�n JavaScript se�ene�ini aktif hale getiriniz.]",

    // Generic message when a block was hit, but the user is still allowed
    // to post an automatically unapproved message.
    "PostingUnapproveError" => "Anti-spam yaz�l�m� mesaj�n�z�n spam olabilece�ini alg�lad�. Bu mesaj� halen g�nderebilirsiniz, fakat forum y�neticileri mesaj� onaylamadan di�er kullan�c�lar taraf�ndan g�r�nt�lenemeyecektir.",

    // Generic message when blocking a message. We do not want to
    // feed specific blocking reasons to those who are blocked, because
    // that info might be used to bypass the blocking reasons.
    "BlockError" => "Anti-spam yaz�l�m� mesaj�n�z�n spam olabilece�ini alg�lad�. Bu y�zden mesaj�n�z engellendi. E�er mesaj�n�z bir spam de�ilse bunun i�in �z�r dileriz. E�er mesaj�n�z�n bloklanmas� ile ilgili halen bir sorun ya��yorsan�z, l�tfen site y�neticileri ile temasa ge�iniz.<br/><br/><b>Not</b>: E�er taray�c�n�zda JavaScript �zelli�ini iptal ettiyseniz ya da taray�c�n�z JavaScript desteklemiyor ise sebep bu olabilir. Baz� anti-spam korumas� gerek�eleri JavaScript ile alakal�d�r.",
);
?>
