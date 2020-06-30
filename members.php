<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.8.0/css/bulma.min.css">
    <link href="style.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat+Alternates:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap"
        rel="stylesheet">
    <title>Конкурс стихотворений "Перо2020"</title>
</head>

<body>
    <header>
        <div class="fon__slogan"><a href="index.html"><img class="fon__img" src="img/konkurs2.png" alt="фоновое изображение"></a></div>
        <div class="menu">
            <div class="menu__container">
                <a href="index.html" class="menu__logo"><img class="logo__img" alt="иконка-лого" src="img/pen.svg"></a>
                <a href="index.html" class="menu__punkt"><img class="mobile-ico" alt="иконка-главная" src="img/award.svg"><span
                        class="punkt-title">О конкурсе</span></a>
                <a href="works.php" class="menu__punkt"><img class="mobile-ico" alt="иконка-стихотворения" src="img/knowledge.svg"><span
                        class="punkt-title">Стихотворения</span></a><a href="members.php" class="menu__punkt"><img
                        class="mobile-ico" alt="иконка-участники" src="img/author.svg"><span class="punkt-title">Участники</span></a>
                <a href="reviews.php" class="menu__punkt"><img class="mobile-ico" alt="иконка-отзывы" src="img/review.svg"><span
                        class="punkt-title">Мои отзывы</span></a>
            </div>
        </div>
    </header>
    <main>
        <div class="members__container">
            
                <?php 
                   $mysqli = mysqli_connect('std-mysql', 'std_953', '12345678', 'std_953');
                   if( mysqli_connect_errno() ) // проверяем корректность подключения
                       echo 'Ошибка подключения к БД: '.mysqli_connect_error();

                    if( isset($_GET['memberId']) ) // переход по ссылке участника
                    {
                        // выполняем поиск записи по ее id
                        $sql_res_member=mysqli_query($mysqli,
                        'SELECT * FROM Members WHERE id='.$_GET['memberId'].' LIMIT 1');
                        $memberROW=mysqli_fetch_assoc($sql_res_member); 
                        echo '<h1>Стихотворения участника '.$memberROW['name'].'</h1>';
                        // кнопка назад ко всем авторам
                        echo '<div class="members__back"><img src="img/arrow.svg" alt="стрелка" class="members__arrow"><a href="members.php">Назад к списку участников</a></div>';

                        //выводим все работы выбранного автора
                        $sql='SELECT * FROM Works, Members, Categories WHERE Members.id = Works.author_id AND Works.category_id = Categories.id AND Members.id ='.$_GET['memberId'];
                        $sql_res=mysqli_query($mysqli, $sql);
                        $i = 1; //переменная для наименования кнопок Отзывы
                        while( $row=mysqli_fetch_assoc($sql_res) ) // пока есть записи
                            {
                                $poem = str_replace('\r\n', "<br>", $row['poem']);
                                echo '
                                <article class="work__block" style="border: 2px solid '.$row['color'].'">
                                    <div class="columns">
                                        <div class="column">
                                        <h2>'.$row['title'].'</h2>
                                        <p>'.$poem.'</p>
                                        </div>
                                        <div class="column">
                                            <p> Категория: <span style="color: '.$row['color'].'">'.$row['category'].'</span></p>
                                            <p> Дата публикации: '.date("d.m.Y", strtotime($row['date'])).'</p>
                                            <p>'.
                                                ($row['gender'] == "м" ? 'Сочинитель: ' : 'Сочинительница: ').$row['name'].'</p>
                                            <p> Возраст: '.$row['age'].'</p>
                                            <p> Город: '.$row['city'].'</p>
                                        </div>
                                    </div>
                                    <div class="comments__component">
                                    <p class="comments__button"><button id="button'.$i.'">Отзывы</button></p>
                                    <div class="work__comments'.$i.'" style="display: none">'; 
                                    $i++;
                                    $work_id =  $row['work_id'];
                                    $sql_comment="SELECT * FROM Reviews, Commentators WHERE Reviews.by_id=Commentators.id AND Reviews.to_poem=".$work_id;
                                    $sql_res_com=mysqli_query($mysqli, $sql_comment);
                                    $sql_myComment = "SELECT * FROM My_review WHERE My_review.poem_id = ".$work_id;
                                    $sql_res_myCom=mysqli_query($mysqli, $sql_myComment);
                                    if ($sql_res_myCom && mysqli_num_rows($sql_res_myCom) != 0)
                                        {    $row_myc=mysqli_fetch_assoc($sql_res_myCom);
                                            echo '<h3>Ваш отзыв</h3><div class="myComment__body">
                                            <p class="comment__title">'.$row_myc['my_name'].' <span class="comment__status"> '.$row_myc['my_status'].'</span> '.date("d.m.Y", strtotime($row_myc['my_date'])).'</p>
                                            <p class="comment__text">'.$row_myc['my_comment'].'</p>
                                            <p class="comment__mark">Оценка: <span class="comment__title">'.$row_myc['my_mark'].'</span></p></div>';}
                                    else echo '<h3>Оставьте свой отзыв</h3><div class="myComment__form">
                                    <form name="form_mycomment" method="post" action="?pg='.$_GET['pg'].'&sort='.$_GET['sort'].'">
                                        <label>Ваша оценка (от 1 до 10): </label>
                                        <input type="number" name="mark" min="1" max="10" required><br>
                                        <label>Комментарий: </label><br>
                                        <textarea class="comment__textarea" rows="4" name="comment" maxlength="500" required placeholder="Ваш комментарий"></textarea><br>
                                        <textarea style="display:none" name="poemId">'.$work_id.'</textarea>
                                        <input type="submit" name="button" class="submit" value="Добавить отзыв">
                                    </form>
                                    </div>';
                                    if ($sql_res_com && mysqli_num_rows($sql_res_com) != 0)
                                    {   
                                        $count_com = mysqli_num_rows($sql_res_com);
                                        echo '<h3>'.$count_com.' '.($count_com == 1 ? 'отзыв' : ($count_com < 5 ? 'отзыва' : 'отзывов')).' других пользователей</h3>';
                                        while( $row_c=mysqli_fetch_assoc($sql_res_com) )
                                        {
                                            echo '<article class="comment__body">
                                            <p class="comment__title">'.$row_c['commentator'].' <span class="comment__status"> '.$row_c['status'].'</span> '.date("d.m.Y", strtotime($row_c['date_review'])).'</p>
                                            <p class="comment__text">'.$row_c['comment'].'</p>
                                            <p class="comment__mark">Оценка: <span class="comment__title">'.$row_c['mark'].'</span></p>
                                            </article>';
                                        };
                                    }
                                    else
                                        echo '<p class="comment__no">У стихотворения пока нет отзывов других пользователей</p>';
                                    echo '</div></div>
                                </article>';
                            };
                    }
                    else 
                    {
                        echo '<h1>Участники</h1>';
                        // формируем и выполняем запрос для получения требуемых полей всех записей таблицы
                        $sql_res_list=mysqli_query($mysqli, 'SELECT * FROM Members');
                        if($sql_res_list) // если запрос успешно выполнен
                        {
                            echo '<div class="members__links">';
                            while( $row=mysqli_fetch_assoc($sql_res_list) ) // перебираем все записи
                            {
                                echo '<a href="?memberId='.$row['id'].'">'.$row['name'].'</a><br>';
                            }
                            echo '</div>';
                        }
                    }
                ?>
        </div>
    </main>
    <footer>
        Конкурс "Перо2020" предоставляет авторам возможность свободной публикации своих литературных произведений в сети
        Интернет на основании пользовательского договора.
        Все авторские права на произведения принадлежат авторам и охраняются законом. Перепечатка произведений возможна
        только с согласия его автора.
        Ответственность за тексты произведений авторы несут самостоятельно на основании правил публикации и
        законодательства Российской Федерации.
    </footer>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="myjs.js"></script>
</body>

</html>