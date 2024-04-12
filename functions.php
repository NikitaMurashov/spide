<?php
  function search_links($query) {
  // Подключаемся к базе данных MySQL
  $conn = new mysqli('localhost', 'root', 'password', 'database');

  // Создаем подготовленное выражение для выборки ссылок
  $stmt = $conn->prepare("SELECT * FROM links WHERE title LIKE ?");

  // Связываем параметр подготовленного выражения со строкой запроса
  $stmt->bind_param('s', $query);

  // Выполняем подготовленное выражение
  $stmt->execute();

  // Получаем результаты
  $result = $stmt->get_result();

  // Инициализируем массив результатов
  $results = [];

  // Преобразуем строку запроса в массив слов
  $query_words = explode(' ', $query);

  // Перебираем результаты
  while ($row = $result->fetch_assoc()) {
    // Получаем заголовок ссылки
    $title = $row['title'];

    // Подсчитываем количество вхождений каждого слова запроса в заголовок
    $word_counts = [];
    foreach ($query_words as $query_word) {
      $word_counts[$query_word] = preg_match_all("/\b" . $query_word . "\b/i", $title, $matches);
    }

    // Считаем общее количество вхождений всех слов запроса в заголовок
    $total_count = array_sum($word_counts);

    // Добавляем ссылку и общее количество вхождений в массив результатов
    $results[] = [
      'url' => $row['url'],
      'count' => $total_count,
    ];
  }

  // Сортируем результаты по убыванию общего количества вхождений
  usort($results, function($a, $b) {
    return $b['count'] - $a['count'];
  });

  // Возвращаем массив результатов
  return $results;
}
?>