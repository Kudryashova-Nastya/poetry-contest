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
        <h1>Стихотворения</h1>
        <div class="columns">
            <div class="column">
                <div class="category__filter">Работы в категории:<br>
                    <a href="?pg=0&sort=love">Любовь</a><br>
                    <a href="?pg=0&sort=philosophy">Философия</a><br>
                    <a href="?pg=0&sort=humor">Юмор</a><br>
                    <a href="?pg=0&sort=home">Родина</a><br>
                    <a href="?pg=0&sort=fan">Фантазия</a><br>
                    <a href="?pg=0&sort=sad">Печаль</a><br>
                    <a href="?pg=0&sort=up">Вдохновение</a><br>
                    <a href="?pg=0&sort=fauna">Природа</a><br>
                    <a href="?pg=0&sort=war">Война</a><br>
                    <a href="?pg=0&sort=child">Для детей</a><br>
                    <a href="?pg=0&sort=all">Все категории</a>
                </div>
            </div>
            <div class="column is-9">
                <?php 
                    // если в параметрах не указана текущая страница – выводим самую первую
                    if( !isset($_GET['pg']) || $_GET['pg']<0 ) $_GET['pg']=0;
                    // если в параметрах не указан тип сортировки или он недопустим
                    if(!isset($_GET['sort']) || ($_GET['sort']!='all' && $_GET['sort']!='love' &&
                    $_GET['sort']!='philosophy' && $_GET['sort']!='humor' && $_GET['sort']!='home' && $_GET['sort']!='fan'
                    && $_GET['sort']!='sad' && $_GET['sort']!='up' && $_GET['sort']!='fauna' && $_GET['sort']!='war' && $_GET['sort']!='child'))
                        $_GET['sort']='all'; // устанавливаем сортировку по умолчанию
                    $type = $_GET['sort'];
                    $page = $_GET['pg'];
                    
                    // осуществляем подключение к базе данных
                    $mysqli = mysqli_connect('std-mysql', 'std_953', '12345678', 'std_953');
                    //$mysqli=pg_connect("host=localhost port=5432 user=postgres password=123 dbname=labaphp") or die("С подключением к базе данных что-то пошло не так"); 
                    if( mysqli_connect_errno() ) // проверяем корректность подключения
                        return 'Ошибка подключения к БД: '.mysqli_connect_error();
                    
                    // Обработчик формы для отправки нашего отзыва
                    if (isset($_POST['poemId']))
                    {
                        $mark = $_POST['mark'];
                        $com = $_POST['comment'];
                        $id = $_POST['poemId'];
                        $sql_res_newCom=mysqli_query($mysqli, "INSERT INTO My_review(poem_id, my_mark, my_date, my_comment) VALUES ($id, $mark, NOW(), '$com')");
                        if (!$sql_res_newCom)
                            echo '<div class="error">При создании отзыва произошла ошибка '.mysqli_errno($mysqli).'. Повторите попытку</div>';
                        $_POST['mark'] = '';
                        $_POST['comment'] = '';
                        $_POST['poemId'] = '';
                    }
                    //Основная часть
                    $sql_res=mysqli_query($mysqli, "SELECT COUNT(*) FROM Works"); //проверяем корректность выполнения запроса и определяем его результат 
                    if( !mysqli_errno($mysqli) && $row=mysqli_fetch_row($sql_res)) 
                        {
                            $TOTAL=$row[0];
                            if(!$TOTAL) // если в таблице нет записей
                                return 'В таблице нет данных' ; // возвращаем сообщение 
                            $PAGES = ceil($TOTAL/10); // вычисляем общее количество страниц 
                            if( $page>=$TOTAL ) // если указана страница больше максимальной
                                $page=$TOTAL-1; // будем выводить последнюю страницу
                            // формируем и выполняем SQL-запрос для выборки записей из БД
                            switch ($type) {
                                case 'all':
                                    $sql="SELECT * FROM Works, Members, Categories WHERE Members.id = Works.author_id AND Works.category_id = Categories.id LIMIT ".($page * 10).", 10";
                                    break;
                                case 'love':
                                    $sql="SELECT * FROM Works, Members, Categories WHERE Members.id = Works.author_id AND Works.category_id = Categories.id AND Works.category_id=1 LIMIT ".($page * 10).", 10";
                                    break;
                                case 'philosophy':
                                    $sql="SELECT * FROM Works, Members, Categories WHERE Members.id = Works.author_id AND Works.category_id = Categories.id AND Works.category_id=2 LIMIT ".($page * 10).", 10";
                                    break;
                                case 'humor':
                                    $sql="SELECT * FROM Works, Members, Categories WHERE Members.id = Works.author_id AND Works.category_id = Categories.id AND Works.category_id=3 LIMIT ".($page * 10).", 10";
                                    break;
                                case 'home':
                                    $sql="SELECT * FROM Works, Members, Categories WHERE Members.id = Works.author_id AND Works.category_id = Categories.id AND Works.category_id=4 LIMIT ".($page * 10).", 10";
                                    break;
                                case 'fan':
                                    $sql="SELECT * FROM Works, Members, Categories WHERE Members.id = Works.author_id AND Works.category_id = Categories.id AND Works.category_id=5 LIMIT ".($page * 10).", 10";
                                    break;
                                case 'sad':
                                    $sql="SELECT * FROM Works, Members, Categories WHERE Members.id = Works.author_id AND Works.category_id = Categories.id AND Works.category_id=6 LIMIT ".($page * 10).", 10";
                                    break;
                                case 'up':
                                    $sql="SELECT * FROM Works, Members, Categories WHERE Members.id = Works.author_id AND Works.category_id = Categories.id AND Works.category_id=7 LIMIT ".($page * 10).", 10";
                                    break;
                                case 'fauna':
                                    $sql="SELECT * FROM Works, Members, Categories WHERE Members.id = Works.author_id AND Works.category_id = Categories.id AND Works.category_id=8 LIMIT ".($page * 10).", 10";
                                    break;
                                case 'war':
                                    $sql="SELECT * FROM Works, Members, Categories WHERE Members.id = Works.author_id AND Works.category_id = Categories.id AND Works.category_id=9 LIMIT ".($page * 10).", 10";
                                    break;
                                case 'child':
                                    $sql="SELECT * FROM Works, Members, Categories WHERE Members.id = Works.author_id AND Works.category_id = Categories.id AND Works.category_id=10 LIMIT ".($page * 10).", 10";
                                    break;
                                default:
                                    echo 'Не указан тип сортировки';
                            }
                            
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
                                
                                if( $PAGES>1 && $_GET['sort']!='love' &&
                                $_GET['sort']!='philosophy' && $_GET['sort']!='humor' && $_GET['sort']!='home' && $_GET['sort']!='fan'
                                && $_GET['sort']!='sad' && $_GET['sort']!='up' && $_GET['sort']!='fauna' && $_GET['sort']!='war' && $_GET['sort']!='child' ) // если страниц больше одной – добавляем пагинацию
                                    {
                                    echo '<div id="pages">'; // блок пагинации
                                        for($i=0; $i<$PAGES; $i++) // цикл для всех страниц пагинации 
                                        if( $i !=$page ) // если не текущая страница
                                            echo '<a href="?pg='.$i.'&sort='.$_GET['sort'].'">'.($i+1).'</a>';
                                        else // если текущая страница
                                            echo '<span>'.($i+1).'</span>';
                                        echo '</div>';
                                    }
                                mysqli_close($mysqli);
                        }
                    // если запрос выполнен некорректно
                    else '<div class="error">Неизвестная ошибка</div>'; // возвращаем сообщение
                    
                ?>
            </div>
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