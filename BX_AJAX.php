<?
//подключаем пролог ядра bitrix
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
//устанавливаем заголовок страницы
$APPLICATION->SetTitle("AJAX");
   //подключаем библиотеку bitrix js: ядро и расширение ajax
   CJSCore::Init(array('ajax'));
   //задаем GET-параметр для проверки запроса
   $sidAjax = 'testAjax'; 
/*обрабатываем запрос: проверяем, что это ajax запрос (проверяем, что глобальная переменная 
$_REQUEST со значением ajax_form существует и равняется переменной sidAjax)*/
if(isset($_REQUEST['ajax_form']) && $_REQUEST['ajax_form'] == $sidAjax){
   //сбрасываем буфер вывода (не выводим заголовок в ответе)
   $GLOBALS['APPLICATION']->RestartBuffer();
   //выводим преобразованный в js массив PHP, cо значениями ключей RESULT и ERROR
   echo CUtil::PhpToJSObject(array(
            'RESULT' => 'HELLO',
            'ERROR' => ''
   ));
   //прерываем выполнение скрипта 
   die();
}

?>
<!--задаем id и значения блоков html -->
<div class="group"> 
   <div id="block"></div >
   <div id="process">wait ... </div >
</div>
<!--выполняем скрипт js-->
<script>
   //устанавливаем вывод расширенной информации в консоли
   window.BXDEBUG = true;
//создаем функцию загрузки
function DEMOLoad(){
    //скрываем блок с id block
   BX.hide(BX("block"));
   //показываем блок с id process
   BX.show(BX("process"));
   //выполняем ajax запрос, загружаем json объект 
   BX.ajax.loadJSON(
      //задаем URL
      '<?=$APPLICATION->GetCurPage()?>?ajax_form=<?=$sidAjax?>',
      //выполняем функцию DEMOResponse
      DEMOResponse
   );
}
//создаем функцию ответа
function DEMOResponse (data){
   //выводим содержимое ответа в консоль
   BX.debug('AJAX-DEMOResponse ', data);
   //отправляем содержимое data.RESULT в элемент с id block
   BX("block").innerHTML = data.RESULT;
   //показываем блок с id block
   BX.show(BX("block"));
   //скрываем блок с id process
   BX.hide(BX("process"));
   //сообщаем о новом событии 
   BX.onCustomEvent(
      BX(BX("block")),
      'DEMOUpdate'
   );
}
//проверяем загруженность и сформированность DOM-структуры
BX.ready(function(){
   /*
   BX.addCustomEvent(BX("block"), 'DEMOUpdate', function(){
      window.location.href = window.location.href;
   });
   */
   //скрываем блоки с id block и process
   BX.hide(BX("block"));
   BX.hide(BX("process"));
    //задаем функцию, которая выполняется при клике на элемент в body с классом css_ajax
    BX.bindDelegate(
      document.body, 'click', {className: 'css_ajax' },
      function(e){
         //проверяем наличие события
         if(!e)
            //если события нет, то используем стандартное из браузера
            e = window.event;
         //выполняем функцию DEMOLoad 
         DEMOLoad();
         //предотвращаем действия браузера по умолчанию
         return BX.PreventDefault(e);
      }
   );
});
</script>
<!--создаем html-элемент click me-->
<div class="css_ajax">click Me</div>
<?
//подключаем эпилог ядра bitrix
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
