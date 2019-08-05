<?php
    $thisPage = 'index';
	
	//Check if file exist before include
	if (is_file('./include/lang.php')) {
		include './include/lang.php';
	}
	else{
		// the file does not exist
		echo 'Language file not found, FATAL ERROR!';
		exit();
	}
?>

<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="Content-Security-Policy" content="">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="description" content="<?=MetaDescription?>">
        <meta name="author" content="<?=MetaAuthor?>">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
		<title><?=Title?></title>
		<link rel="stylesheet" type="text/css" href="vendor/semantic.min.css">
        <style>
            .hidden.menu{
                display: none;
            }
            .pushable{
                overflow-x: visible;
            }
            ui.top.menu{
               background-color: #f00;
            } 
            ui.top.borderless.menu{
                background-color: #0ff;
            }
            .ui.tabular.menu{
                background-color: #e5e8ea;
            }
            .ui.tabular.menu .item{
                color: #000;
                font-size: 1.25rem;
            }
            .ui.tabular.menu .item:hover{
                color: #0094ff;
            }
            .ui.tabular.menu .active.item:hover{
                color: #000;
                cursor: default;
            }                        
            .ui.tabular.menu .active.item{
                background-color: #e2dcc5;
            }
            .tab.segment{
                background-color: #e2dcc5;    
            }
            #header h1{
                font-size: 3.5vmax;
                letter-spacing: 1.5px;
                font-weight: 800;
                
                    
            }
            #slogan{
                font-style: italic;
                text-shadow: 3px 5px 2px rgba(75, 74, 74, 0.8);
                letter-spacing: 2px;
                font-size: 1.8vmax;
            }
            #cdcbooks{
                padding-top: 10px;
            }
            #cdcbooks h1{
                font-size: 2.5vmax;
                letter-spacing: 0.1em;
            }
            #cdcbooks h4{
                font-size: 1.5vmax;
                letter-spacing: 0.1em;
            }            
            #footer{
                background-color: #e3e0e7;     
            }
            #footer h4{
                text-decoration: underline;
            }
            #footerlinks a{
                font-size: larger;
                color: #000;
            }
            #siteby{
                letter-spacing: 1px;
                margin-right: 5px;
                margin-top: 5px;
            }           
            #return-to-top i {
                    position: fixed;
                    bottom: 20px;
                    right: 20px;
                    color: rgba(75, 74, 74, 0.8);
                    margin: 0;
                    display: none;
                    font-size: 28px;
                    text-shadow: 0px 10px 0px rgb(33,133,208);
                    -webkit-transition: all 0.3s ease;
                    -moz-transition: all 0.3s ease;
                    -ms-transition: all 0.3s ease;
                    -o-transition: all 0.3s ease;
                    transition: all 0.3s ease;
            }

            #return-to-top:hover i {
                    color: rgb(33,133,208);
                    text-shadow: 0px 10px 0px rgba(75, 74, 74, 0.8);
    
            }
			
            @media only screen and (max-width: 700px) {
                .ui.tabular.menu .item{
                    font-size: 1rem;
                }
                #cdcbooks h1{
                    font-size: 3.5vmax;
                }
                #cdcbooks h4{
                    font-size: 2.5vmax;
                }
                #slogan{
                    
                }
            }
        </style>
	</head>
	<body>

        <!--Top menu bar starts-->
		<div class="ui inverted blue top attached borderless huge menu">
			<a class="header item" id="sidebaricon"><i class="sidebar icon"></i>&nbsp;<?=Menu1?></a>
			<div class="ui simple dropdown right item">
				<?=Menu2?>&nbsp;<i class="translate icon"></i>
				<div class="menu">
					<a href="<?=$_SERVER['PHP_SELF'].'?language=en'?>" class="item"><i class="us flag"></i><?=Lang1?></a>
					<a href="<?=$_SERVER['PHP_SELF'].'?language=ne'?>" class="item"><i class="nepal flag"></i><?=Lang2?></a>
				</div>
			</div>
		</div>
		
        <div class="ui top fixed hidden menu">
			<div class="ui simple dropdown right item">
				<?=Menu2?>&nbsp;<i class="translate icon"></i>
				<div class="menu">
					<a href="<?=$_SERVER['PHP_SELF'].'?language=en'?>" class="item"><i class="us flag"></i><?=Lang1?></a>
					<a href="<?=$_SERVER['PHP_SELF'].'?language=ne'?>" class="item"><i class="nepal flag"></i><?=Lang2?></a>
				</div>
			</div>
        </div>
        <!--Top menu bar ends-->


        <div class="ui bottom attached segment pushable">
            <!--Side menu bar starts-->
            <div class="ui inverted blue labeled icon left inline vertical sidebar menu">
			    <a href="index.php?language=<?=$lang?>" class="active item"><i class="university icon"></i><?=Smenu1?></a>
			    <a class="item"><i class="student icon"></i><?=Smenu2?></a>
			    <a class="item" id="contributelink"><i class="world icon"></i><?=Smenu3?></a>
			    <a class="item" id="contactlink"><i class="mail outline icon"></i><?=Smenu4?></a>
		    </div>
		    <!--Side menu bar ends--> 
            
            <div class="pusher">
                <!--Modal window for contribute menu starts-->
                <div class="ui large modal" id="contribute">
                    <i class="close icon"></i>
                    <div class="header"><?=Modal1header?></div>
                    <div class="content"><?=Modal1content?></div> 
                </div>
                <!--Modal window for contribute menu ends-->

                <!--Modal window for contact menu starts-->
                <div class="ui large modal" id="contact">
                    <i class="close icon"></i>
                    <div class="header"><?=Modal2header?></div>
                    <div class="content"><?=Modal2content?></div> 
                </div>                    
                <!--Modal window for contact menu ends-->

                <!--Header contents starts-->
                <div id="header">
                    <h1 class="ui center aligned blue icon header">
                        <?=Name?><i class="circular inverted blue users icon"></i>
		            </h1>
                    <h2 class="ui center aligned header" id="slogan"><?=Header1?></h2>
		        </div>
                <!--Header contents ends-->
				
                <!--Book display layout contents starts-->
                <div id="cdcbooks">
                    <div>
                        <hr/>
                        <h1 class="ui center aligned header"><?=Bookcategory1?></h1>
                        <h4 class="ui center aligned header"><?=Bookcategorydetail1?></h4>
                        <hr/>
                    </div>
					
                    <?php
						//Check if file exist before include
						if (is_file('./display.php')) {
							include './display.php';
						}
						else{
							// the file does not exist
							echo 'Book-display template file not found, FATAL ERROR!';
							exit();
						}					
					?>
					
				</div>
				<!--Book display layout contents ends-->
        
				<!--Footer starts-->	
                <div class="ui vertical footer segment" id="footer">
	                <div class="ui container">
		                <div class="ui stackable  divided equal height grid">
			                <div class="six wide column">
                                <div class="content">
                                    <h4 class="ui  header"><?=Leftfootertitle?></h4>
                                    <div class="description">
                                        <p><?=Leftfootercontent01?></p>
                                    </div>
                                </div>
			                </div>
			                <div class="four wide column">
                                <div class="content">
				                    <h4 class="ui  header"><?=Centerfootertitle?></h4>
				                    <div class="ui description list" id="footerlinks">
					                    <a href="#" class="item"><i class="facebook black icon"></i><?=Centerfootercontent01?></a>
					                    <a href="#" class="item"><i class="youtube black  icon"></i><?=Centerfootercontent02?></a>
					                    <a href="#" class="item"><i class="github black  icon"></i><?=Centerfootercontent03?></a>
				                    </div>
                                </div>
			                </div>
			                <div class="six wide column">
				                <h4 class="ui  header"><?=Rightfootertitle?></h4>
				                <form class="ui form">
                                    <div class="two fields">
                                      <div class="field">
                                        <label><?=Formlabel01?></label>
                                        <input type="text" name="first-name" placeholder="<?=Formplaceholder01?>" required>
                                      </div>
                                      <div class="field">
                                        <label><?=Formlabel02?></label>
                                        <input type="email" placeholder="<?=Formplaceholder02?>" required>
                                      </div>
                                    </div>
                                  <button class="ui blue button" type="submit"><?=Formbutton01?></button>
                                </form>
			                </div>
		                </div>
                    </div> 
                </div>
                <!--Footer ends-->
				<h5 class="ui right aligned header" id="siteby">&copy;&nbsp;<a href="mailto:cregmi@abo.fi" target="mail"><?=Copyrighttext?></a></h5>
            </div>
        </div>
        
		<a href="javascript:" id="return-to-top"><i class="chevron up icon"></i></a>
		
		<script src="vendor/jquery.min.js"></script>
		<script src="vendor/semantic.min.js"></script>
		<script>
			$(document).ready(function () {
				
				$('.tabular .item').tab();
				
				$('.special.cards .image').dimmer({ on: 'hover' });
				
				$('.ui.sidebar.menu')
					.sidebar({ context: $('.bottom.attached.segment.pushable') })
					.sidebar({scrollLock: true})
					.sidebar('attach events', '#sidebaricon')
				;
				
				$('.top.huge.menu')
					.visibility({once: false,onBottomPassed: function () {
						$('.fixed.menu').transition('fade in');
					},
					onBottomPassedReverse: function () {
						$('.fixed.menu').transition('fade out');
					}
					})
				;
				
				$('#contribute')
					.modal('setting', 'transition','vertical flip')
					.modal('attach events', '#contributelink', 'show')
				;
				
				$('#contact')
					.modal('setting', 'transition','vertical flip')
					.modal('attach events', '#contactlink', 'show')
				;

				$(window).scroll(function() {
					if ($(this).scrollTop() >= 500) {        
						$('#return-to-top i').fadeIn(200);    
						
					} else {
						$('#return-to-top i').fadeOut(200);   
					}
				});
				
				$('#return-to-top').click(function() {     
					$('body,html').animate({
						scrollTop : 0                       
					}, 500);
				});
			});
			
        </script>
	</body>
</html>
