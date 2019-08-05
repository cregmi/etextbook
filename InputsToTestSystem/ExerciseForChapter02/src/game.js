/// <reference path="http://code.createjs.com/createjs-2013.12.12.min.js" />
/// <reference path="../../../Content/GamesDownloadTemplate/lib/ScormHelper.js" />
var Game = Game || (function (createjs, $) {
    var standardTextShadow = new createjs.Shadow("gray", 1, 1, 3);


    function Game(canvasId, gameData) {
        gameData = gameData || {};
        var assetsPath = gameData.assetsPath || "";
        var gameBoard = null;
        var assets = [
           { id: "backGround", src: assetsPath + "triviaGameBackground.jpg" },
           { id: "instructions", src: assetsPath + "TriviatronInstructions.png" },
           { id: "Happy", src: assetsPath + "rightAnswer.png" },
           { id: "Sad", src: assetsPath + "wrongAnswer.png" },
           { id: "topTitle", src: assetsPath + "titleTopHalf.png" },
           { id: "bottomTitle", src: assetsPath + "titleBottomHalf.png" },
           { id: "closeBtn", src: assetsPath + "closeBtnSprite.png" },
           { id: "infoBtn", src: assetsPath + "infoBtnSprite.png" },
           { id: "startBtn", src: assetsPath + "start_button.png" },
           { id: "musicOn", src: assetsPath + "musicOn.png" },
           { id: "musicOff", src: assetsPath + "musicOff.png" },

        ];

        //adding audio
        createjs.Sound.registerSound({ src: assetsPath + "TriviaTronAudio/triviatron_mechanical.mp3", id: "mechanicalSound" });
        createjs.Sound.registerSound({ src: assetsPath + "TriviaTronAudio/triviatron_button.mp3", id: "buttonSound" });
        createjs.Sound.registerSound({ src: assetsPath + "TriviaTronAudio/triviatron_positive.mp3", id: "positiveSound" });
        createjs.Sound.registerSound({ src: assetsPath + "TriviaTronAudio/triviatron_negative.mp3", id: "negativeSound" });
        createjs.Sound.registerSound({ src: assetsPath + "TriviaTronAudio/triviatron_click.mp3", id: "clickSound" });
        createjs.Sound.registerSound({ src: assetsPath + "TriviaTronAudio/triviatron_BotWelcome.mp3", id: "welcomeSound" });
        createjs.Sound.registerSound({ src: assetsPath + "TriviaTronAudio/triviatron_CylonWelcome.mp3", id: "welcomeCylonSound" });
        createjs.Sound.registerSound({ src: assetsPath + "TriviaTronAudio/triviatron_gameover.mp3", id: "gameover" });
        var mechanical = createjs.Sound.createInstance("mechanicalSound");

        var queue = new createjs.LoadQueue(false);
        queue.addEventListener("complete", function (event) {
            mechanical.play();
            initializeGame();
        });
        queue.loadManifest(assets);

        var isLmsConnected = false;
        var currentLmsInteraction = null;

        if (typeof ScormHelper !== 'undefined') {
            isLmsConnected = ScormHelper.initialize();
        }

        var quit;

        if (isLmsConnected) {
            quit = function () {
                createjs.Sound.play("clickSound");

                ScormHelper.cmi.exit("");
                ScormHelper.adl.nav.request("exitAll");
                ScormHelper.terminate();
            }
        }
        else {
            quit = function () {
                createjs.Sound.play("clickSound");

                window.location = "http://www.wisc-online.com";
            }
        }

        var TitleView = new createjs.Container();
        var NumberOfPlayers = new createjs.Container();
        var GameView = new createjs.Container();
        var isTwoPlayer = false;

        var gameState = {
            player1Score: 0,
            player1Name: "Player 1",
            gameOn: false,
            currentQuestion: "",
            usedQuestionCount: 0,
            playerCount: 1,
            musicOn: true,
        }
        function isMobil() {
            return (navigator.userAgent.match(/Android/i)
                     || navigator.userAgent.match(/webOS/i)
                     || navigator.userAgent.match(/iPhone/i)
                     || navigator.userAgent.match(/iPad/i)
                     || navigator.userAgent.match(/iPod/i)
                     || navigator.userAgent.match(/BlackBerry/i)
                     || navigator.userAgent.match(/Windows Phone/i));

        }
        function initializeGame() {

            var stage = new createjs.Stage(canvasId);
            stage.enableMouseOver();

            // addTitleView();
            addWelcomeView();
            //ticker
            createjs.Ticker.setFPS(60);
            createjs.Ticker.addEventListener("tick", updateStage);

            var tickCount = 0;

            //only plays when playing again...
            this.onloadstart = createjs.Sound.play("mechanicalSound");

            function updateStage() {

                stage.update();

            }


            function addWelcomeView() {

                var welcomeContain = new createjs.Container();
               
                var blankBackground = new createjs.Shape(new createjs.Graphics().beginFill("silver").drawRect(0, 0, 800, 600).endFill());
                var GameBackground = new createjs.Bitmap(queue.getResult("backGround"));
                stage.addChild(blankBackground, GameBackground, welcomeContain);

                var top = new createjs.Bitmap(queue.getResult("topTitle"));
                var bottom = new createjs.Bitmap(queue.getResult("bottomTitle"));

                GameBackground.y = 600;
                top.y = -390;
                bottom.y = 600;
                welcomeContain.addChild(bottom, top);
                var topTween = createjs.Tween.get(top);
                var bottomTween = createjs.Tween.get(bottom);
                addSoundControl(welcomeContain)
                topTween.to({ y: 0 }, 1000);
                bottomTween.to({ y: 261 }, 1000).call(function () {               
                    var startButton = new createjs.Bitmap(queue.getResult("startBtn"));
                    var startButton = new createjs.Container();
                    startButton.addChild(new createjs.Shape(new createjs.Graphics().setStrokeStyle(5).beginStroke("black").beginRadialGradientFill([ "#EB1700", "black" ], [ 0, 1 ], 50, 50, 15, 50, 50, 50).drawCircle(50, 50, 50).endStroke()));
                    startButton.addChild(new createjs.Shape(new createjs.Graphics().beginRadialGradientFill([ "#EB1700", "yellow" ], [0, 1], 50, 50, 7.5, 50, 50, 2).drawCircle(50, 50, 10).endStroke()));
                  
                    var greenStartButton = new createjs.Bitmap(queue.getResult("startBtn"));
                    greenStartButton.shadow = new createjs.Shadow("gray", 3, 3, 3);
                    greenStartButton.hitArea = new createjs.Shape(new createjs.Graphics().beginFill("#f00").drawCircle(50, 50, 50));
                    greenStartButton.cursor = 'pointer';
                    greenStartButton.regX = 50;
                    greenStartButton.regY = 50;
                    greenStartButton.x = 725;
                    greenStartButton.y = 525;
                    greenStartButton.on("click", function () {
                        startGame();
                    }, this, false);

                    startButton.hitArea = new createjs.Shape(new createjs.Graphics().beginFill("#f00").drawCircle(50, 50, 50));
                    startButton.cursor = 'pointer';
                    startButton.regX = 50;
                    startButton.regY = 50;
                    startButton.x = 402;
                    startButton.y = 385;
                    startButton.on("mouseover", handleStartButtonHover);
                    startButton.on("mouseout", handleStartButtonHover);
                    greenStartButton.on("mouseover", handleStartButtonHover);
                    greenStartButton.on("mouseout", handleStartButtonHover);
                    welcomeContain.addChild(startButton, greenStartButton);
                    var infodata = {
                        images: [queue.getResult("infoBtn")],
                        frames: { width: 52, height: 53 },
                        animations: { normal: [0, 0], selected: [1, 1] }
                    };
                    infoButton = new createjs.Sprite(new createjs.SpriteSheet(infodata), "normal")
                    infoButtonContainer = new createjs.Container();
                    infoButtonContainer.hitArea = infoButton;
                    infoButtonContainer.on("mouseover", spriteMouseAction, this, false, { action: "selected", obj: infoButton });
                    infoButtonContainer.on("mouseout", spriteMouseAction, this, false, { action: "normal", obj: infoButton });
                    infoButtonContainer.on("click", function () {
                        var info = new InstructionsView();
                    }, this, false);
                    infoButton.x = 10;
                    infoButton.y = 525;
                    infoButton.scaleX = 1.25;
                    infoButton.scaleY = 1.25;
                    infoButtonContainer.addChild(infoButton);
                    welcomeContain.addChild(infoButtonContainer);
                  
                    function startGame() {
                        createjs.Sound.play("clickSound");
                        var topTween = createjs.Tween.get(top);
                        var bottomTween = createjs.Tween.get(bottom);
                        var startTween = createjs.Tween.get(startButton);
                        var bgTween = createjs.Tween.get(GameBackground);
                        var infoTween = createjs.Tween.get(infoButtonContainer);
                        var greenTween = createjs.Tween.get(greenStartButton);
                        topTween.to({ y: -390 }, 1000);
                        startTween.to({ y: 725 }, 1000); // 725 position for it to travel at the proper speed
                        greenTween.to({y:725}, 500)
                        infoTween.to({ y: 600 }, 1000);
                        bgTween.to({ y: 0 }, 1000);
                        bottomTween.to({ y: 600 }, 1000).call(addGameView);
                    }
                    this.onload = createjs.Sound.play("welcomeSound");
                    startButton.on("click", function () {
                        startGame();
                    });
                   
                })
                function spriteMouseAction(event, v) {
                    v.obj.gotoAndStop(v.action);
                }

                function handleStartButtonHover(event) {
                    if (event.type == "mouseover") {
                        createjs.Tween.get(event.currentTarget).to({ scaleX: 1.12, scaleY: 1.12 }, 150).to({ scaleX: 1.0, scaleY: 1.0 }, 150).to({ scaleX: 1.12, scaleY: 1.12 }, 150).to({ scaleX: 1.0, scaleY: 1.0 }, 150);
                    }
                    else {
                        createjs.Tween.get(event.currentTarget).to({ scaleX: 1.0, scaleY: 1.0 }, 100);
                    }
                }



            }
            function addGameView() {
                stage.removeChild(TitleView);
                //welcome
                var GameBackground = new createjs.Bitmap(queue.getResult("backGround"));
                GameView.addChild(GameBackground);
                addSoundControl(GameView);
                gameBoard = new GameBoard(GameView);
                gameBoard.init();
                gameBoard.showNextQuestion();
                stage.addChild(GameView);
            }
            function InstructionsView() {

                var instructionsContainer = new createjs.Container();
                instructionsContainer.name = "instructions";
                var bg = new createjs.Bitmap(queue.getResult("instructions"));


                var hit = new createjs.Shape();
                var exitContainer = new createjs.Container();
                var exitBox = new createjs.Shape();

                exitContainer.x = 720;
                exitContainer.y = 570;
                var exitText = new createjs.Text("BACK", 'bold 18px Arial', "#fff");
                exitText.x = 8;
                exitText.y = 8;
                exitContainer.hitArea = new createjs.Shape(new createjs.Graphics().beginFill("#7449AE").beginStroke("#000").setStrokeStyle(1).drawRoundRect(0, 0, 70, 37, 5).endFill().endStroke());
                hit.graphics.beginFill("#000").drawRect(0, 0, exitText.getMeasuredWidth(), exitText.getMeasuredHeight());
                exitBox.graphics.beginFill("#AD0E11").beginStroke("#000").setStrokeStyle(1).drawRoundRect(0, 0, 70, 37, 5).endFill().endStroke();
                exitText.hitArea = hit;
                exitContainer.addChild(exitBox, exitText);

                instructionsContainer.addChild(bg, exitContainer);//, 
                stage.addChild(instructionsContainer);

                exitContainer.addEventListener("click", function (event) {
                    stage.removeChild(instructionsContainer);
                });
            }
            function GameBoard(container) {
                this.container = container;
                this.questions = gameData.Questions.slice();


                this.isGameOver = false;
                this.totalPoints = new createjs.Text("POINTS:", "bold 19px Arial", "#868686");              
                this.totalPoints.x = 480;
                this.totalPoints.y = 545;
                this.container.addChild(this.totalPoints)
                this.QuestionNumber = new createjs.Text("Question ", "bold 19px Arial", "#868686");
                this.QuestionNumber.x = 150;
                this.QuestionNumber.y = 545;
                this.container.addChild(this.QuestionNumber)
                var _currentQuestion = -1;
                Object.defineProperty(this, "currentQuestion", {
                    get: function () { return _currentQuestion; },
                    set: function (value) {
                        if (_currentQuestion != -1)
                            this.container.removeChild(_currentQuestion.contain);
                        _currentQuestion = null;
                        _currentQuestion = value;
                        this.QuestionNumber.text = "Question " + (gameData.Questions.length- this.questions.length)  + " / " + gameData.Questions.length
                    }
                });
                var _score;
                var _currentQuestion = -1;
                Object.defineProperty(this, "score", {
                    get: function () { return _score; },
                    set: function (value) {                       
                        _score = value;
                        this.totalPoints.text = "Points: " + _score;
                        gameState.player1Score = _score;
                    }
                });
                this.score = 0;

                var infodata = {
                    images: [queue.getResult("infoBtn")],
                    frames: { width: 52, height: 53 },
                    animations: { normal: [0, 0], selected: [1, 1] }
                };
                this.infoButton = new createjs.Sprite(new createjs.SpriteSheet(infodata), "normal")
                this.infoButtonContainer = new createjs.Container();
                this.infoButtonContainer.hitArea = this.infoButton;
                this.infoButtonContainer.on("mouseover", spriteMouseAction, this, false, { action: "selected", obj: this.infoButton });
                this.infoButtonContainer.on("mouseout", spriteMouseAction, this, false, { action: "normal", obj: this.infoButton });
                this.infoButtonContainer.on("click", function () {
                    var info = new InstructionsView();
                }, this, false);
                this.infoButton.x = isMobil()? 323:  360;
                this.infoButton.y = 532;
                this.infoButtonContainer.addChild(this.infoButton);
                this.container.addChild(this.infoButtonContainer);

                var closedata = {
                    images: [queue.getResult("closeBtn")],
                    frames: { width: 52, height: 53 },
                    animations: { normal: [0, 0], selected: [1, 1] }
                };
                if (isMobil()) {
                this.closeButton = new createjs.Sprite(new createjs.SpriteSheet(closedata), "normal")
                this.closeButtonContainer = new createjs.Container();
                this.closeButtonContainer.hitArea = this.closeButton;
                this.closeButtonContainer.on("mouseover", spriteMouseAction, this, false, { action: "selected", obj: this.closeButton });
                this.closeButtonContainer.on("mouseout", spriteMouseAction, this, false, { action: "normal", obj: this.closeButton });
                this.closeButtonContainer.on("click", quit, this, false);
                this.closeButton.x = 388;
                this.closeButton.y = 532;
                this.closeButtonContainer.addChild(this.closeButton)
                }

                this.container.addChild(this.closeButtonContainer);
                this.init = function () {
                    // calculate point values                   
                }
                function spriteMouseAction(event, v) {
                    v.obj.gotoAndStop(v.action);
                }
                GameBoard.prototype.showNextQuestion = function () {
                    this.submittedScore = false;
                    if (this.questions.length == 0) {
                        this.gameOver()
                        return;
                    }
                    this.currentQuestion = new Question(this.questions.shift(), this);
                }
                $(window).bind('beforeunload', function () {
                    submitScore(gameBoard)
                })
                function submitScore(gameBoard) {
                    if (gameBoard.submittedScore)
                        return false;
                    gameBoard.submittedScore = true;

                    var url = gameData.leaderboardUrl;
                    if (url) {
                        var data = { gameId: gameData.id, score: gameState.player1Score };
                        $.ajax(url, {
                            type: "POST",
                            data: data,
                            success: function (x) { },
                            error: function (x, y, z) { }
                        });
                    }

                }
                GameBoard.prototype.gameOver = function () {
                    var gameover = createjs.Sound.play("gameover");
                    submitScore(this)                     
                    if (isLmsConnected) {
                        ScormHelper.cmi.successStatus(ScormHelper.successStatus.passed);
                        ScormHelper.cmi.completionStatus(ScormHelper.completionStatus.completed);
                    }
                    if (isLmsConnected || isMobil()) {
                        if (isLmsConnected) {
                            this.currentQuestion = new Question({ Text: "Game Over", Answers: [{ Text: "Quit", click: quit }] }, gameBoard);
                        } else {
                            this.currentQuestion = new Question({ Text: "Game Over", Answers: [{ Text: "Re-play", click: replay }, { Text: "Quit", click: quit }] }, gameBoard);
                        }
                    } else {
                        this.currentQuestion = new Question({ Text: "Game Over", Answers: [{ Text: "Re-play", click: replay }] }, this);
                    }
                }

            }

        }
        function replay() {
            gameBoard = null;
            createjs.Sound.play("buttonSound");
            initializeGame();  //wow that was easy
        }
        function Question(question, gameBoard) {
            createjs.Sound.play("clickSound");

            if (question.Id) { // seeing from above, we create a new Question for the quit menu, but that question won't have an ID
                if (isLmsConnected) {
                    currentLmsInteraction = ScormHelper.cmi.interactions().new();
                    currentLmsInteraction.id = question.Id;
                    currentLmsInteraction.description = question.Text;
                    currentLmsInteraction.type = ScormHelper.interactions.choice;
                }
            }

            this.gameBoard = gameBoard;
            this.x = 370;
            this.y = 50;
            this.align = "center";
            this.width = 550;
            this.questionFont = "bold 24px Arial";
            this.questionColor = "#4DE74A";
            this.answerColor = "#4DE74A";
            this.answerFont = "bold 40px Arial";
            this.answered = false;
            this.question = question;
            this.contain = new createjs.Container();

            this.questionText = new createjs.Text(question.Text, this.questionFont);
            this.questionText.x = this.x;
            this.questionText.y = this.y;
            this.questionText.textAlign = this.align;
            this.questionText.color = this.questionColor;
            this.questionText.lineWidth = this.width;
            //this.questionText.regY = this.questionText.getMeasuredHeight() / 2;
            var curpx = 24;
            while (this.questionText.getMeasuredHeight() > 95) {
                this.questionText.font = curpx + "px Arial Black";
                curpx = curpx - 1
                if (curpx == 10) break;
            }
            this.answers = [];
            var currentY = 150;
            this.contain.addChild(this.questionText);
            for (var j = 0 ; j < question.Answers.length; j++) {
                var a = new createjs.Text(question.Answers[j].Text, this.questionFont, this.answerColor);
                a.y = currentY;
                a.x = this.x;
                a.lineWidth = this.width - 20;
                a.textAlign = this.align;
                currentY += 20 + a.getMeasuredHeight();
                this.answers.push(a);

                var bg = new createjs.Shape(new createjs.Graphics().beginFill("#999").drawRoundRect((a.x - 4) - this.width / 2, a.y - 4, this.width, a.getMeasuredHeight() + 8, 8));

                bg.id = "Answer" + j;
                var answerContainer = new createjs.Container();
                answerContainer.hitArea = bg;
                answerContainer.correct = question.Answers[j].IsCorrect;
                answerContainer.click = question.Answers[j].click;
                var answerBackground = new createjs.Shape(new createjs.Graphics().beginFill("#333").beginStroke("#006837").drawRoundRect((a.x - 4) - this.width / 2, a.y - 4, this.width, a.getMeasuredHeight() + 8, 8));


                answerContainer.text = a;
                answerContainer.question = question;
                answerContainer.gameBoard = this.gameBoard;
                answerContainer.answer = question.Answers[j];
                a.hitArea = bg;
                answerContainer.addChild(answerBackground);
                answerContainer.addChild(a);
                this.contain.addChild(answerContainer);
                this.gameBoard.container.addChild(this.contain);

                function changeBGcolor(evt) {
                    for (var x = 0 ; x < evt.currentTarget.parent.children.length; x++) {
                        var theItem = evt.currentTarget.parent.children[x];
                        if (theItem.correct == false) {
                            var a = theItem.text;
                            a.color = "#000";
                            theItem.removeChild(oldbox);
                            theItem.removeChild(a);
                            var bg = new createjs.Shape();
                            bg.shadow = standardTextShadow;
                            theItem.addChild(bg);
                            theItem.addChild(a);
                        } else if (theItem.correct == true) {
                            var oldbox = theItem.children[0];
                            var a = theItem.text;
                            a.color = "#4DE74A";
                            theItem.removeChild(oldbox);
                            var bg = new createjs.Shape(new createjs.Graphics().beginFill("#000").beginStroke("#4DE74A").drawRoundRect(a.x - 556 / 2, a.y - 5, 550, a.getMeasuredHeight() + 10, 8));
                            bg.shadow = standardTextShadow;
                            evt.currentTarget.addChild(bg);
                            evt.currentTarget.addChild(a);
                        }

                    }

                }
                answerContainer.addEventListener("click", function (evt) {

                    if (currentLmsInteraction != null) {
                        var a = evt.currentTarget.answer;

                        currentLmsInteraction.result = a.IsCorrect ? ScormHelper.results.correct : ScormHelper.results.incorrect;
                        currentLmsInteraction.learnerResponse = a.Text;
                        currentLmsInteraction.save();
                        currentLmsInteraction = null;
                    }

                    var board = evt.currentTarget.gameBoard;
                    if (evt.currentTarget.click) {
                        evt.currentTarget.click();
                        return;
                    }
                    if (board.currentQuestion.answered) {
                        return;
                    }
                    board.currentQuestion.answered = true;
                    board.currentQuestion.showResults(evt.currentTarget.correct);
                    changeBGcolor(evt);
                   
                });

            }
        }
        Question.prototype.showResults = function (isCorrect) {
            if (isCorrect) {
                createjs.Sound.play("positiveSound");
                this.gameBoard.score = this.gameBoard.score + 100;
            }
            if (!isCorrect) {
                createjs.Sound.play("negativeSound");
            }
            //Happy or sad face 
            var feedBackImg = new createjs.Bitmap(queue.getResult(isCorrect ? "Happy" : "Sad"));
            feedBackImg.x = 100;
            feedBackImg.y = 430;
            this.contain.addChild(feedBackImg);

            //Feedback area
            var feed = this.gameBoard.currentQuestion.question.Feedback ? this.gameBoard.currentQuestion.question.Feedback : "";
            feedback = new createjs.Text(feed, "18px Arial", "white");
            feedback.x = 180;
            feedback.y = 420;
            feedback.lineWidth = 325;
            this.contain.addChild(feedback);

            var bg = new createjs.Shape();
            bg.graphics.beginFill("powderblue");
            bg.graphics.drawRoundRect(525, 445, 150, 50, 5);

            var nextTxt = new createjs.Text("Next >", "bold 40px Arial", "white");
            nextTxt.x = 540;
            nextTxt.y = 445;

            var nextContainer = new createjs.Container();
            nextContainer.hitArea = bg;
            nextTxt.hitArea = bg;
            nextContainer.on("click", function () {
                createjs.Sound.play("clickSound");
                this.gameBoard.showNextQuestion();
            }, this, this);

            this.contain.addChild(nextContainer, nextTxt);
            //feedback

        }
        function getCategoryById(id) {
            for (var j = 0 ; j < gameData.Categories.length; j++)
                if (gameData.Categories[j].Id == id)
                    return gameData.Categories[j].Name;
            return "";
        }
        function addSoundControl(container) {
            var soundContainer = new createjs.Container();
            soundContainer.x = 0;
            soundContainer.y = 0;
            soundContainer.hitArea = new createjs.Shape(new createjs.Graphics().beginFill("#F00").drawCircle(0, 50, 50));
            soundContainer.cursor = 'pointer';
            var sound = new createjs.Bitmap(queue.getResult(gameState.musicOn ? "musicOn" : "musicOff"));
            sound.name = "music";
            //sound.scaleX = .75;
            //sound.scaleY = .75;
            soundContainer.addChild(sound);
            createjs.Sound.setMute(!gameState.musicOn);
            container.addChild(soundContainer);
            soundContainer.addEventListener("click", function (evt) {
                gameState.musicOn = !gameState.musicOn;
                var sound = new createjs.Bitmap(queue.getResult(gameState.musicOn ? "musicOn" : "musicOff"));
                sound.name = "music"
                //sound.scaleX = .75;
                //sound.scaleY = .75;
                var destroy = evt.currentTarget.getChildByName("music");
                evt.currentTarget.removeChild(destroy);
                evt.currentTarget.addChild(sound);
                createjs.Sound.setMute(!gameState.musicOn);
            });
        }

    }
    return Game;
})(createjs, $);
