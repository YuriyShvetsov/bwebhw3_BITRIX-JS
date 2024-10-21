<?
//подключаем пролог ядра bitrix
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
//устанавливаем заголовок страницы
$APPLICATION->SetTitle("AJAX");
//подключает библиотеку Bitrix JS
   CJSCore::Init(array('ajax'));
   $sidAjax = 'testAjax';
   /*
   Проверяем, был ли отправлен запрос и есть ли в запросе поле 'ajax_form' со значением 'testAjax'
   Если есть, то очищаем буфер ввода перед выводом ajax-результата, чтобы скрипт получал чистые данные без вставок шаблона header и footer
   Преобразуем массив с ответом в json.
   */
if(isset($_REQUEST['ajax_form']) && $_REQUEST['ajax_form'] == $sidAjax){
   $GLOBALS['APPLICATION']->RestartBuffer();
   echo CUtil::PhpToJSObject(array(
            'RESULT' => 'HELLO',
            'ERROR' => ''
   ));
   die();
}

?>
<!-- создаем 2 блока на странице -->
<div class="group">
   <div id="block"></div >
   <div id="process">wait ... </div >
</div>
<!-- JavaScript -->
<script>
    <!-- включаем режим отладки -->
   window.BXDEBUG = true;

    <!--
        функция скрывает на странице блок с id="block"
        и показывает блок id="process"
        Загружает json-объект из отправленной формы "testAjax" передаёт его обработчику "DEMOResponse"
     -->
function DEMOLoad(){
   BX.hide(BX("block"));
   BX.show(BX("process"));
   BX.ajax.loadJSON(
      '<?=$APPLICATION->GetCurPage()?>?ajax_form=<?=$sidAjax?>',
      DEMOResponse
   );
}

<!--
   Обработчик отправляет в консоль полученные данные из формы,
   подставляет на страницу в блок с id="block" данные из формы,
   выводит на странице блок с id="block"
   и скрывает блок id="process"
-->
function DEMOResponse (data){
   BX.debug('AJAX-DEMOResponse ', data);
   BX("block").innerHTML = data.RESULT;
   BX.show(BX("block"));
   BX.hide(BX("process"));

   BX.onCustomEvent(
      BX(BX("block")),
      'DEMOUpdate'
   );
}
<!--
   После полной загрузки страницы скрываются блоки с id="block" и id="process"
   на элемент с классом "css_ajax" устанавливается обработчик события 'click'.
   При наступлении события, выполняется функция DEMOLoad() и отключается поведение браузера по умолчанию для данного события
-->

BX.ready(function(){
   /*
   BX.addCustomEvent(BX("block"), 'DEMOUpdate', function(){
      window.location.href = window.location.href;
   });
   */
   BX.hide(BX("block"));
   BX.hide(BX("process"));
   
    BX.bindDelegate(
      document.body, 'click', {className: 'css_ajax' },
      function(e){
         if(!e)
            e = window.event;
         
         DEMOLoad();
         return BX.PreventDefault(e);
      }
   );
   
});

</script>
<!--
   Добавление на страницу блока с классом "css_ajax"
-->
<div class="css_ajax">click Me</div>
<?
//подключаем эпилог ядра bitrix
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
