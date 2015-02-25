<?
// ������� ����� /bitrix/php_interface/include/form/validators/valid_http.php

class CFormValidHttp
{
  function GetDescription()
  {
    return array(
      "NAME"            => "valid_http",                                   // �������������
      "DESCRIPTION"     => "�������� http",                                 // ������������
      "TYPES"           => array("text", "textarea"),                            // ���� �����
      "SETTINGS"        => array("CFormValidHttp", "GetSettings"), // �����, ������������ ������ ��������
      "CONVERT_TO_DB"   => array("CFormValidHttp", "ToDB"),        // �����, �������������� ������ �������� � ������
      "CONVERT_FROM_DB" => array("CFormValidHttp", "FromDB"),      // �����, �������������� ������ �������� � ������
      "HANDLER"         => array("CFormValidHttp", "DoValidate")   // ���������
    );
  }

  function GetSettings()
  {
    return array(
      "CHECK_HTTP" => array(
        "TITLE"   => "�� ������ ��������� (ftp|http|https)",
        "TYPE"    => "CHECKBOX",
        "DEFAULT" => "Y",
      ),
      "CHECK_URL" => array(
        "TITLE"   => "�� ������ ��������� (domain.ru | �����.��)",
        "TYPE"    => "CHECKBOX",
        "DEFAULT" => "Y",
      ),
      "CHECK_LinkHrefUrl" => array(
        "TITLE"   => "�� ������ ��������� ([url] | [href] | [link])",
        "TYPE"    => "CHECKBOX",
        "DEFAULT" => "Y",
      ),
    );
  }

  function ToDB($arParams)
  {
    // ���������� ��������������� ������
    $arParams["CHECK_HTTP"] = $arParams["CHECK_HTTP"] == "Y" ? "Y" : "N";
    $arParams["CHECK_URL"] = $arParams["CHECK_URL"] == "Y" ? "Y" : "N";
    $arParams["CHECK_LinkHrefUrl"] = $arParams["CHECK_LinkHrefUrl"] == "Y" ? "Y" : "N";

    return serialize($arParams);
  }

  function FromDB($strParams)
  {
    // ������� �������������� �� ���������, ������ ������ ����������������� ������
    return unserialize($strParams);
  }

  function DoValidate($arParams, $arQuestion, $arAnswers, $arValues)
  {
    global $APPLICATION;
    // ���������� ���������
    $urlPattern = "/(ftp|http|https):\/\/?/";
    $justURL = "/([a-z�-�]+)\.([a-z�-�]{2})/";
    $notLinkHrefUrl = "/(<a.+)(\/a>)|(\[url).+(\[url\])|(\[link).+(\[\/link\])/";

    foreach ($arValues as $value)
    {
      // ������ �������� ����������
      if (strlen($value) <= 0) continue;

      // �������� http
      if ($arParams["CHECK_HTTP"] == "Y" && preg_match($urlPattern , $value))
      {
        // ������ ������
        $APPLICATION->ThrowException("#FIELD_NAME#: ���� �������� ������");
        return false;
      }

      // �������� justURL
      if ($arParams["CHECK_URL"] == "Y" && preg_match($justURL , $value))
      {
        // ������ ������
        $APPLICATION->ThrowException("#FIELD_NAME#: ���� �������� ������");
        return false;
      }

      // �������� notLinkHrefUrl
      if ($arParams["CHECK_LinkHrefUrl"] == "Y" && preg_match($notLinkHrefUrl , $value))
      {
        // ������ ������
        $APPLICATION->ThrowException("#FIELD_NAME#: ���� �������� ������������ �������");
        return false;
      }

    }
    // ��� �������� ������ ���������, ������ true
    return true;
  }
}
AddEventHandler("form", "onFormValidatorBuildList", array("CFormValidHttp", "GetDescription"));
?>