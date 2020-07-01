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
        <div class="reviews__container">
            
                <?php 
                   $mysqli = mysqli_connect('std-mysql', 'std_953', '12345678', 'std_953');
                   if( mysqli_connect_errno() ) // проверяем корректность подключения
                       echo 'Ошибка подключения к БД: '.mysqli_connect_error();

                    // если были переданы данные для изменения записи в таблице
                    if( isset($_POST['button']) && $_POST['button'] == 'Изменить отзыв')
                    {
                        // формируем и выполняем SQL-запрос на изменение записи с указанным id
                        $sql_res=mysqli_query($mysqli, "UPDATE My_review SET my_name='".
                        htmlspecialchars($_POST['name'])."', my_comment='".
                        htmlspecialchars($_POST['comment'])."', my_mark=".
                        $_POST['mark']." WHERE id=".$_GET['id']);
                        echo '<div class="ok in-edit">Данные успешно изменены</div>'; // и выводим сообщение об изменении данных
                    }

                    $currentROW=array(); // информации о текущей записи пока нет
                    // если id текущей записи передано –
                    if( isset($_GET['id']) ) // (переход по ссылке или отправка формы)
                    {
                        // выполняем поиск записи по ее id
                        $sql_res=mysqli_query($mysqli,
                        'SELECT * FROM My_review WHERE id='.$_GET['id'].' LIMIT 1');
                        $currentROW=mysqli_fetch_assoc($sql_res); // информация сохраняется для дальнейшего изменения
                        echo '<div class="stub"></div>';
                    }
                    
                    // формируем и выполняем запрос для получения требуемых полей всех записей таблицы
                    $sql_res=mysqli_query($mysqli, 'SELECT * FROM My_review, Works WHERE Works.work_id = My_review.poem_id');
                    if($sql_res) // если запрос успешно выполнен
                    {
                        echo '<table id="edit_links"><thead><tr><th></th><th></th><th>ID</th><th>Имя</th><th>Комментарий</th><th>Оценка</th><th>Стихотворение</th></tr></thead>';
                        while( $row=mysqli_fetch_assoc($sql_res) ) // перебираем все записи выборки
                        {
                            // если текущая запись пока не найдена и её id не передан
                            // или передан и совпадает с проверяемой записью
                            if($currentROW['id']==$row['id'])
                                // значит в цикле сейчас текущая запись
                                echo '<tr class="activeReview">';
                            else echo '<tr>';// если проверяемая в цикле запись не текущая
                            echo '<td class="special"><a class="to_edit" href="?id='.$row['id'].'">Изменить</a></td>
                            <td class="special"><a class="to_delete" href="?deleteId='.$row['id'].'">Удалить</a></td>
                            <td aria-label="ID">'.$row['id'].' </td>
                            <td aria-label="Имя"> '.$row['my_name'].' </td>
                            <td aria-label="Комментарий">'.$row['my_comment'].' </td>
                            <td aria-label="Оценка"> '.$row['my_mark'].' </td>
                            <td aria-label="Стихотворение"> '.$row['poem'].' </td></tr>';
                        }
                        echo '</table>';
                        
                        if( $_GET['id'] ) // если есть текущая запись
                        {
                            // формируем HTML-код формы
                            echo '
                            <div class="edit_form">
                            <fieldset>
                        <legend> Изменить отзыв </legend>
                            <form name="form_edit" method="post" action="?id='.$currentROW['id'].'">
                            <label for="name">Имя</label>
                            <input type="text" maxlength="64" name="name" id="name" value="'.
                            $currentROW['my_name'].'" required><br>
                            <textarea class="com" maxlength="500" name="comment" placeholder="Ваш комментарий" required>'.
                            $currentROW['my_comment'].'</textarea><br>
                            <label for="mark">Ваша оценка</label>
                            <input type="number" name="mark" min="1" max="10" id="mark" value="'.
                            $currentROW['my_mark'].'" required><br>
                            <input type="submit" name="button"
                            value="Изменить отзыв"></form></fieldset></div>';
                        };
                        mysqli_close($mysqli);
                    }
                    else // если запрос не может быть выполнен
                        echo 'Ошибка базы данных'; // выводим сообщение об ошибке
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
</body>

</html>