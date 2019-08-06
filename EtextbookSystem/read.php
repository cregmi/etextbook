<?php
    $thisPage='read';
	
	//Check if file exist before include
	if (is_file('./include/lang.php')) {
		include './include/lang.php';
	}
	else{
		// the file does not exist
		echo 'Language file not found, FATAL ERROR!';
		exit();
	}
	
	if(!isset($_GET['id']) || $_GET['id']==null){
		exit("Identifier Missing");
	}
	
	//Check if file exist before include
	if (is_file('./include/config.php')) {
		include './include/config.php';
	}
	else{
		// the file does not exist
		echo 'Database configuration file not found, FATAL ERROR!';
		exit();
	}	
	
	$bookId = intval($_GET['id']);
	
	$bookQueryString = "SELECT * FROM book WHERE id=?";
	$bookQuery = $databaseHandle->prepare($bookQueryString);
	$bookQuery->bindParam(1, $bookId, PDO::PARAM_INT);
	$bookQuery->execute();
	$bookRecordAll = $bookQuery->fetch();
	if($bookRecordAll){
		$bookFileName = $bookRecordAll['pdf_file_name'];
		$bookName = $bookRecordAll['name'];
		$bookTag = $bookRecordAll['description_tag'];
	}else{
		exit('No record found for given ID');
	}
	
	$pdfFile = './upload/book/pdf/'.$bookFileName;
	if (!file_exists($pdfFile)) {
		exit('PDF file not found');
	}
	
	$bookMapQueryString = "SELECT * FROM book_map WHERE book_id=?";
	$bookMapQuery = $databaseHandle->prepare($bookMapQueryString);
	$bookMapQuery->bindParam(1, $bookId, PDO::PARAM_INT);
	$bookMapQuery->execute();
	$pageForLesson = array();
	$lessonFileReference = array();
	$pageForExercise = array();
	$exerciseFileReference = array();
	
	while($bookMap = $bookMapQuery->fetch()){
		$chapterId[] = intval($bookMap['chapter_id']);
		$chapterStartPage[] = intval($bookMap['chapter_start_page']);
		$chapterEndPage[] = intval($bookMap['chapter_end_page']);
		$pageForLesson[] = intval($bookMap['chapter_end_page']);
		$pageForExercise[]= intval($bookMap['chapter_end_page']);
		
		if($bookMap['lesson_id']!=NULL){
			$lessonId = intval($bookMap['lesson_id']); 
			$lessonFileQueryString = "SELECT * from lesson WHERE id=?";
			$lessonFileQuery = $databaseHandle->prepare($lessonFileQueryString);
			$lessonFileQuery->bindParam(1,$lessonId, PDO::PARAM_INT);
			$lessonFileQuery->execute();
			$lessonRecordAll = $lessonFileQuery->fetch();
			$lessonFileReference[] = $lessonRecordAll['content_file_name'];
		}
		else{
			$lessonFileReference[] = NULL;
		}
		
		if($bookMap['exercise_id']!=NULL){
			$exerciseId = intval($bookMap['exercise_id']); 
			$exerciseFileQueryString = "SELECT * from exercise WHERE id=?";
			$exerciseFileQuery = $databaseHandle->prepare($exerciseFileQueryString);
			$exerciseFileQuery->bindParam(1,$exerciseId, PDO::PARAM_INT);
			$exerciseFileQuery->execute();
			$exerciseRecordAll = $exerciseFileQuery->fetch();
			$exerciseFileReference[] = $exerciseRecordAll['content_file_name'];
		}
		else{
			$exerciseFileReference[] = NULL;
		}
	}

?>

<!DOCTYPE html>
<html lang="en">
    <head>			
		<meta charset="utf-8">
		<meta http-equiv="Content-Security-Policy" content="">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="description" content="online interactive library of school textbooks">
		<meta name="author" content="Chandan Regmi, email: info@textbookslibrary.com">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
		<link rel="stylesheet" type="text/css" href="./vendor/semantic.min.css">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Reading, <?=$bookName?>, <?=str_replace('_',' ',$bookTag)?></title>
		<style>
			canvas{
				width:49%;
				height:100%;
			}
			dialog{
				height:95%;
				width:95%;
			}
			dialog>h4{
				text-align:center;
			}
			.pageControls{
				text-align:center;
			}
			.theBook{
				box-sizing: border-box;
				border: 5px solid black;
				height:93.3vh;
				width:100vw
			}
			#leftPageCanvas{
				border-right: 1px solid black;
			}
			#rightPageCanvas{
				border-left: 1px solid black;
			}
			#cancelDialog{
				float:right;
			}
			#newTab{
				float:right;
			}
			
			#explainButtonLeft, #playButtonLeft{float:left;}
			#explainButtonRight, #playButtonRight{float:right;}
			#pageNumber{text-align:center;}

			.hide{
				display: none;
			}
			.multimediaCanvas { 
			   display: block;
			   width: 100%; height: 90%;
			   background: #000; 
			}
			.mediaIframe{
				height:98%;
				width:100%;
			}


		</style>
    </head>
	<body>
		<script src="//mozilla.github.io/pdf.js/build/pdf.js"></script>
		<div class="ui container">
			<?php 
				$i=0; 
				echo '<p style="text-align:center;"><b>Chapters -> </b>'; 
				foreach($chapterId as $id){ 
					echo '<button class="ui circular compact mini inverted secondary button" id="linkForChapter'.$id.'" value="'.$chapterStartPage[$i].'">'.$id.'</button>'; 
					$i++; 
				} 
				echo '</p>';
			?>
		</div>
		
		<div class="theBook">
			<canvas id="leftPageCanvas"></canvas>
			<canvas id="rightPageCanvas"></canvas>
		</div>
		
		<dialog id="mediaDialog">
			<a href="" target="_blank" id="newTab"><button><i class="external alternate icon"></i></button></a>
			<button id="cancelDialog"><i class="close icon"></i></button>
				<iframe sandbox="allow-same-origin allow-scripts allow-pointer-lock allow-presentation allow-popups"  class="mediaIframe" 
						id="mediaContainer"
						title="Iframe"
						frameborder="1"
						scrolling="no"
						marginheight="0"
						marginwidth="0"
						src="">
				</iframe>
		</dialog>
		
		
		<div class="ui grid">
			<div class="four wide column">
				<button id="explainButtonLeft" class="hide">
					<?=ExplainText?>
					<i class="cubes icon"></i>
				</button>
				<button id="playButtonLeft" class="hide">
					<?=PlayText?>
					<i class="cubes icon"></i>
				</button>
			</div>
			<div class="eight wide column" id="pageNumber">
				<div class="ui three column grid">
					<div class="column">
						<button class = "ui tiny button" id="prev"><?=PreviousButton?></button>
					</div>
					<div class="column">
						<span style="font-size:12px"><?=Page?>
							<span id="leftPageNumber"></span>,
							<span id="rightPageNumber"></span>
							&nbsp;|&nbsp;<?=TotalPages?>
							<span id="page_count"></span>
						</span>
					</div>
					<div class="column">
						<button class = "ui tiny button" id="next"><?=NextButton?></button>
					</div>
				</div>
			</div>
			<div class="four wide column">
				<button id="explainButtonRight" class="hide">
					<?=ExplainText?>
					<i class="cubes icon"></i>
				</button>
				<button id="playButtonRight" class="hide">
					<?=PlayText?>
					<i class="cubes icon"></i>
				</button>
			</div>
		</div>
		<script>
			//Based on, https://mozilla.github.io/pdf.js/.
			var explainText = <?="'".ExplainText."'"?>,
				playText = <?="'".PlayText."'"?>;

			var pdfFile = <?="'".$pdfFile."'"?>,
				pageForLesson = <?php echo json_encode($pageForLesson); ?>,
				lessonFileReference = <?php echo json_encode($lessonFileReference); ?>,
				pageForExercise = <?php echo json_encode($pageForExercise); ?>,
				exerciseFileReference = <?php echo json_encode($exerciseFileReference); ?>;
				chapterId = <?php echo json_encode($chapterId); ?>;

			var explainButtonLeft = document.getElementById('explainButtonLeft'),
				playButtonLeft = document.getElementById('playButtonLeft'),
				explainButtonRight = document.getElementById('explainButtonRight'),
				playButtonRight = document.getElementById('playButtonRight'),
				mediaContainer = document.getElementById('mediaContainer'),
				mediaDialog = document.getElementById('mediaDialog'),
				cancelDialogButton = document.getElementById('cancelDialog'),
				newTabButton = document.getElementById('newTab'),
				buttonStyleClass = 'ui labeled icon positive tiny button';


			// Loaded via <script> tag, create shortcut to access PDF.js exports.
			var pdfjsLib = window['pdfjs-dist/build/pdf'];

			// The workerSrc property shall be specified.
			pdfjsLib.GlobalWorkerOptions.workerSrc = '//mozilla.github.io/pdf.js/build/pdf.worker.js';

			var pdfDoc = null,
				pageNum = 1,
				pageRendering = false,
				pageNumPending = null,
				scale = 1,
				leftCanvas = document.getElementById('leftPageCanvas'),
				leftContext = leftCanvas.getContext('2d'),
				rightCanvas = document.getElementById('rightPageCanvas'),
				rightContext = rightCanvas.getContext('2d');

			/**
			 * Get page info from document, resize canvas accordingly, and render page.
			 * @param num Page number.
			 */
			function renderPage(num) {

				//Hide buttons on page rendering
				explainButtonLeft.setAttribute('class', 'hide');
				playButtonLeft.setAttribute('class', 'hide');
				explainButtonRight.setAttribute('class', 'hide');
				playButtonRight.setAttribute('class', 'hide');

				mediaContainer.src = '';

				pageRendering = true;
				// Using promise to fetch the page
				pdfDoc.getPage(num).then(function(page) {
					var viewport = page.getViewport(scale);
					leftCanvas.height = viewport.height;
					leftCanvas.width = viewport.width;

					// Render PDF page into canvas context
					var renderContext = {
						canvasContext: leftContext,
						viewport: viewport
					};
					var renderTask = page.render(renderContext);

					// Wait for rendering to finish
					renderTask.promise.then(function() {
						pageRendering = false;
						if (pageNumPending !== null) {
							// New page rendering is pending
							renderPage(pageNumPending);
							pageNumPending = null;
						}
					});
				});

				pageRendering = true;
				// Using promise to fetch the page
				pdfDoc.getPage(num + 1).then(function(page) {
					var viewport = page.getViewport(scale);
					rightCanvas.height = viewport.height;
					rightCanvas.width = viewport.width;

					// Render PDF page into canvas context
					var renderContext = {
						canvasContext: rightContext,
						viewport: viewport
					};
					var renderTask = page.render(renderContext);

					// Wait for rendering to finish
					renderTask.promise.then(function() {
						pageRendering = false;
						if (pageNumPending !== null) {
							// New page rendering is pending
							renderPage(pageNumPending);
							pageNumPending = null;
						}
					});
				});
				// Update page counters
				document.getElementById('leftPageNumber').textContent = num;
				document.getElementById('rightPageNumber').textContent = num + 1;

				var i = 0;
				for (var p of pageForLesson) {
					if (lessonFileReference[i] != null) {
						if (num == Number(p)) {
							explainButtonLeft.setAttribute('class', buttonStyleClass);
							var lessonFile = lessonFileReference[i];
							explainButtonLeft.addEventListener('click', function() {
								mediaDialog.open = false;
								mediaDialog.showModal();
								mediaContainer.src = './upload/lesson/' + lessonFile;
								newTabButton.href = './upload/lesson/' + lessonFile;
							});
						} else if (num + 1 == Number(p)) {
							explainButtonRight.setAttribute('class', buttonStyleClass);
							var lessonFile = lessonFileReference[i];
							explainButtonRight.addEventListener('click', function() {
								mediaDialog.open = false;
								mediaDialog.showModal();
								mediaContainer.src = './upload/lesson/' + lessonFile;
								newTabButton.href = './upload/lesson/' + lessonFile;
							});
						}
					}
					i = i + 1;
				}

				var i = 0;
				for (var p of pageForExercise) {
					if (exerciseFileReference[i] != null) {
						if (num == Number(p)) {
							playButtonLeft.setAttribute('class', buttonStyleClass);
							var exerciseFile = exerciseFileReference[i];
							playButtonLeft.addEventListener('click', function() {
								mediaDialog.open = false;
								mediaDialog.showModal();
								mediaContainer.src = './upload/exercise/' + exerciseFile;
								newTabButton.href = './upload/exercise/' + exerciseFile;
							});

						} else if (num + 1 == Number(p)) {
							playButtonRight.setAttribute('class', buttonStyleClass);
							var exerciseFile = exerciseFileReference[i];
							playButtonRight.addEventListener('click', function() {
								mediaDialog.open = false;
								mediaDialog.showModal();
								mediaContainer.src = './upload/exercise/' + exerciseFile;
								newTabButton.href = './upload/exercise/' + exerciseFile;
							});
						}
					}
					i = i + 1;
				}

				cancelDialogButton.addEventListener('click', function() {
					mediaContainer.src = '';
					mediaDialog.close();
				});
				newTabButton.addEventListener('click', function() {
					mediaContainer.src = '';
					mediaDialog.close();
				});
			}

			/**
			 * If another page rendering in progress, waits until the rendering is
			 * finised. Otherwise, executes rendering immediately.
			 */
			function queueRenderPage(num) {
				if (pageRendering) {
					pageNumPending = num;
				} else {
					renderPage(num);
				}
			}

			/**
			 * Displays previous page.
			 */
			function onPrevPage() {
				if (pageNum <= 1) {
					return;
				}
				pageNum = pageNum - 2;
				queueRenderPage(pageNum);
			}
			document.getElementById('prev').addEventListener('click', onPrevPage);
			document.getElementById('leftPageCanvas').addEventListener('click', onPrevPage);

			/**
			 * Displays next page.
			 */
			function onNextPage() {
				if (pageNum >= pdfDoc.numPages) {
					return;
				}
				pageNum = pageNum + 2;
				queueRenderPage(pageNum);
			}
			document.getElementById('next').addEventListener('click', onNextPage);
			document.getElementById('rightPageCanvas').addEventListener('click', onNextPage);
			
			
			/*new function for chapter navigation*/
			function onChapter() {
				pageNum = parseInt(this.value);
				queueRenderPage(pageNum);
			}			
			
			for (var c of chapterId ) {
				var buttonId = 'linkForChapter' + c;
				document.getElementById(buttonId).addEventListener('click', onChapter);
			}
			
			/**
			 * Asynchronously downloads PDF.
			 */
			pdfjsLib.getDocument(pdfFile).then(function(pdfDoc_) {
				pdfDoc = pdfDoc_;
				document.getElementById('page_count').textContent = pdfDoc.numPages;

				// Initial/first page rendering
				renderPage(pageNum);
			});
		</script>
	</body>
</html>
