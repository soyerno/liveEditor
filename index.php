<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Live Editor</title>
	<style type="text/css">
	#pageContainer{
		position: relative;
		width: 100%;
		margin-top: 0px;
		background: blue;
		margin: 0;
		padding: 0;
	}
	.editZone{
		position: relative;
		width: 100%;
		height: 280px;
		border-bottom: 5px solid blue;
		margin: 0;
		padding: 0;
		transition: 2s;
	}
	.editZone.inactive{
		width: 100%;
		margin-top: -290px;
	}
	.element{
		float: left;
		position: relative;
		height: 300px;
		width: 50%;
		background: yellow;
		margin: 0;
		padding: 0;
	}
	.element:first-child {background: red}
	.element:nth-child(2n+3) {background: #CCC}
	#close{
		position: relative;
		width: 100%;
		height: 20px;
		background: silver;
	}
	</style>
</head>
<body>
	<div class="editZone inactive">
			<div id="close"></div>
			<div id="mceZone"></div>
	</div>
	<div id="themeSelectorContainer">
		<select id="themeSelector">
			<?php
			$directorio = opendir("templates"); //ruta actual
			while ($archivo = readdir($directorio)) //obtenemos un archivo y luego otro sucesivamente
			{
			    if (is_dir($archivo))//verificamos si es o no un directorio
			    {
			        //echo "[".$archivo . "]<br />"; //de ser un directorio lo envolvemos entre corchetes
			    }
			    else
			    {
			        echo "<option>".$archivo . "</option>";
			    }
			}
			?>
		</select>
	</div>
	<div id="pageContainer">
		<div class="element"></div>	
		<div class="element"></div>
	</div>
	
	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<script type="text/javascript" src="js/tinymce/jquery.tinymce.min.js"></script>
	<script type="text/javascript" src="js/tinymce/tinymce.min.js"></script>
	<script type="text/javascript">
	var liveEditorController = {
		'params' : {
			'mceInitialized' : false
		},
		'init' : function(){
			console.log('liveEditorController->init');
			
			this.openEditor();
			this.closeEditor();
			this.themeSelectorListener();
		},
		closeEditor : function(){
			$("#close").on('click',function(){
				$(".element.active").removeClass('active');
				$(".editZone").addClass('inactive');
				setTimeout(function(){
					//tinyMCE.activeEditor.destroy();
				}, 2000);
			});
		},
		'openEditor' : function(){
			$(".element").on('click', function(){
				var self = $(this),
					index = self.index();
					$(".element.active").removeClass('active');
					$(this).addClass('active');
					$(".editZone").removeClass('inactive');
					if(liveEditorController.params.mceInitialized == false){
						liveEditorController.startTinyMCE();							
					}else{
						liveEditorController.setActiveElementContent();	
					}
			});
		},
		'themeSelectorListener' : function(){
			$("#themeSelector").on('change', function(){
				var selected = $('#themeSelector option:selected').val();
				console.log(selected);
				liveEditorController.getTheme(selected);
			});
		},
		'getTheme' : function(selected){
			$.ajax({
			  url: "templates/"+selected,
			  context: document.body
			}).done(function(data) {
			   $("#pageContainer").empty().html(data);
			});
		},
		setActiveElementContent : function(){
			var elementContent = $(".element.active").html();
			tinyMCE.activeEditor.setContent(elementContent, {format : 'raw'});
		},
		startTinyMCE : function(){
			console.log("Initializing TinyMCE");
			tinymce.init({
			    selector: "#mceZone",
			    theme: "modern",
			    width: '100%',
			    height: 150,
			    plugins: [
			         "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
			         "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
			         "save table contextmenu directionality emoticons template paste textcolor"
			   ],
			   content_css: "css/content.css",
			   toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | l      ink image | print preview media fullpage | forecolor backcolor emoticons", 
			   style_formats: [
			        {title: 'Bold text', inline: 'b'},
			        {title: 'Red text', inline: 'span', styles: {color: '#ff0000'}},
			        {title: 'Red header', block: 'h1', styles: {color: '#ff0000'}},
			        {title: 'Example 1', inline: 'span', classes: 'example1'},
			        {title: 'Example 2', inline: 'span', classes: 'example2'},
			        {title: 'Table styles'},
			        {title: 'Table row 1', selector: 'tr', classes: 'tablerow1'}
			    ],
			    init_instance_callback : "liveEditorController.setActiveElementContent",
			    setup: function(editor) {
			        editor.on('change', function(e) {
			            console.log('change event', e);
			            var content = tinyMCE.activeEditor.getContent({format : 'raw'});
			            $(".element.active").html(content);
			        });
			    }
			 });
			liveEditorController.params.mceInitialized = true;
		}
	}
	liveEditorController.init();
	</script>
</body>
</html>