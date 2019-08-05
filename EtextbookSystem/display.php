<?php
	if(!isset($thisPage)){
		die();
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
	
	$bookTagsQuery = $databaseHandle->query('SELECT DISTINCT description_tag FROM book');
	
	echo '<div class="ui top attached tabular centered grid menu">';
	
	$count = 0;
	$allBooktags = array();
	$active = "active";
	
	while($eachBooktag = $bookTagsQuery->fetch()){
		$allBooktags[] = $eachBooktag;
		$bookCategory = str_replace('_',' ',$eachBooktag['description_tag']);
		$count = $count+1;
		echo '<a class="item '.$active.'" data-tab="'.$count.'">'.$bookCategory.'</a>';
		$active = NULL;
		
	}	
	
	echo'</div>';
	
	$tab_count = count($allBooktags);
	
	if($tab_count>0){
		$active = "active";
		$count = 0;
		foreach($allBooktags as $eachBooktag ){
				
			$count = $count+1;
			$booktag = $eachBooktag['description_tag'];
				
			$booksQueryString = "SELECT * FROM book WHERE description_tag=? ORDER BY name ASC";
			$booksQuery = $databaseHandle->prepare($booksQueryString);
			$booksQuery->bindParam(1, $booktag, PDO::PARAM_STR);
			$booksQuery->execute();
				
			echo '
				<div class="ui bottom attached tab segment '.$active.'" data-tab="'.$count.'">
					<div class="ui special cards centered">
				';
			$active = NULL;
				
			while($eachBook = $booksQuery->fetch()){
				if($eachBook['image_file_name']!=NULL){
					$bookImage = './upload/book/image/'.$eachBook['image_file_name'];
				}
				else{
					$bookImage = './images/logo.png';
				}
				$bookName = str_replace('_',' ',$eachBook['name']);
				$bookLanguage = $eachBook['language_tag'];
				echo 
				'
						<div class="ui card">
							<div class="blurring dimmable image">
								<div class="ui dimmer">
									<div class="content">
										<div class="center">';
											echo '<a href="read.php?id='.$eachBook['id'].'" target="_blank"><button class="ui blue button" style="margin-bottom:4px;"><i class="large book icon"></i>'.Read.'</button></a>';											
									echo '</div>
									</div>
								</div>
								<img class="image" src="'.$bookImage.'" alt="book">
							</div>
							<div class="content">
								<h2 class="header">'.$bookName.'<span style="font-size:12px;"> ('.$bookLanguage.')</span></h2>
							</div>
						</div>
				';
			}

			echo '
					</div>
				</div>
				';
		}		
	}
	else{
		echo'<div class="ui divider" style="min-height:150px;">
				<h4 class="ui center aligned red header">EMPTY DATABASE</h4>
			</div>';
	}
		
?>

