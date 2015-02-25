<?
// Листинг файла /bitrix/php_interface/include/form/validators/valid_http.php

class CFormValidHttp
{
  function GetDescription()
  {
    return array(
      "NAME"            => "valid_http",                                   // идентификатор
      "DESCRIPTION"     => "Проверка http",                                 // наименование
      "TYPES"           => array("text", "textarea"),                            // типы полей
      "SETTINGS"        => array("CFormValidHttp", "GetSettings"), // метод, возвращающий массив настроек
      "CONVERT_TO_DB"   => array("CFormValidHttp", "ToDB"),        // метод, конвертирующий массив настроек в строку
      "CONVERT_FROM_DB" => array("CFormValidHttp", "FromDB"),      // метод, конвертирующий строку настроек в массив
      "HANDLER"         => array("CFormValidHttp", "DoValidate")   // валидатор
    );
  }

  function GetSettings()
  {
    return array(
      "CHECK_HTTP" => array(
        "TITLE"   => "Не должен содержать (ftp|http|https)",
        "TYPE"    => "CHECKBOX",
        "DEFAULT" => "Y",
      ),
      "CHECK_URL" => array(
        "TITLE"   => "Не должен содержать (domain.ru | домен.рф)",
        "TYPE"    => "CHECKBOX",
        "DEFAULT" => "Y",
      ),
      "CHECK_LinkHrefUrl" => array(
        "TITLE"   => "Не должен содержать ([url] | [href] | [link])",
        "TYPE"    => "CHECKBOX",
        "DEFAULT" => "Y",
      ),
    );
  }

  function ToDB($arParams)
  {
    // возвращаем сериализованную строку
    $arParams["CHECK_HTTP"] = $arParams["CHECK_HTTP"] == "Y" ? "Y" : "N";
    $arParams["CHECK_URL"] = $arParams["CHECK_URL"] == "Y" ? "Y" : "N";
    $arParams["CHECK_LinkHrefUrl"] = $arParams["CHECK_LinkHrefUrl"] == "Y" ? "Y" : "N";

    return serialize($arParams);
  }

  function FromDB($strParams)
  {
    // никаких преобразований не требуется, просто вернем десериализованный массив
    return unserialize($strParams);
  }

  function DoValidate($arParams, $arQuestion, $arAnswers, $arValues)
  {
    global $APPLICATION;
    // Регулярные выражения
    $urlPattern = "/(ftp|http|https):\/\/?/";
    $justURL = "/([a-zа-я]+)\.([a-zа-я]{2})/";
    $notLinkHrefUrl = "/(<a.+)(\/a>)|(\[url).+(\[url\])|(\[link).+(\[\/link\])/";

    foreach ($arValues as $value)
    {
      // пустые значения пропускаем
      if (strlen($value) <= 0) continue;

      // проверим http
      if ($arParams["CHECK_HTTP"] == "Y" && preg_match($urlPattern , $value))
      {
        // вернем ошибку
        $APPLICATION->ThrowException("#FIELD_NAME#: Поле содержит ссылку");
        return false;
      }

      // проверим justURL
      if ($arParams["CHECK_URL"] == "Y" && preg_match($justURL , $value))
      {
        // вернем ошибку
        $APPLICATION->ThrowException("#FIELD_NAME#: Поле содержит ссылку");
        return false;
      }

      // проверим notLinkHrefUrl
      if ($arParams["CHECK_LinkHrefUrl"] == "Y" && preg_match($notLinkHrefUrl , $value))
      {
        // вернем ошибку
        $APPLICATION->ThrowException("#FIELD_NAME#: Поле содержит недопустимые символы");
        return false;
      }

    }
    // все значения прошли валидацию, вернем true
    return true;
  }
}
AddEventHandler("form", "onFormValidatorBuildList", array("CFormValidHttp", "GetDescription"));
?>