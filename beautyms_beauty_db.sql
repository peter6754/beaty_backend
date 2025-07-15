-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Хост: localhost:3306
-- Время создания: Июл 15 2025 г., 21:56
-- Версия сервера: 8.0.42-0ubuntu0.24.04.1
-- Версия PHP: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `beautyms_beauty_db`
--

-- --------------------------------------------------------

--
-- Структура таблицы `auth_assignment`
--

CREATE TABLE `auth_assignment` (
  `item_name` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `user_id` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Дамп данных таблицы `auth_assignment`
--

INSERT INTO `auth_assignment` (`item_name`, `user_id`, `created_at`) VALUES
('admin', '1', 1752496260),
('admin', '14', 1725434768),
('admin', '2', 1720082643),
('client', '10', 1725385064),
('client', '11', 1725385624),
('client', '12', 1725386955),
('client', '13', 1725434661),
('client', '14', 1725434682),
('client', '15', 1744806814),
('client', '16', 1744818428),
('client', '17', 1746176247),
('client', '2', 1752497067),
('client', '3', 1723657368),
('client', '4', 1724687584),
('client', '5', 1724687607),
('client', '6', 1724687626),
('client', '7', 1724690762),
('client', '8', 1725284455),
('client', '9', 1725385045),
('master', '3', 1752497640);

-- --------------------------------------------------------

--
-- Структура таблицы `auth_code`
--

CREATE TABLE `auth_code` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `code` int NOT NULL,
  `date` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `auth_item`
--

CREATE TABLE `auth_item` (
  `name` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `type` smallint NOT NULL,
  `description` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `rule_name` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `data` blob,
  `created_at` int DEFAULT NULL,
  `updated_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Дамп данных таблицы `auth_item`
--

INSERT INTO `auth_item` (`name`, `type`, `description`, `rule_name`, `data`, `created_at`, `updated_at`) VALUES
('/admin/*', 2, NULL, NULL, NULL, 1720082523, 1720082523),
('/debug/*', 2, NULL, NULL, NULL, 1720082539, 1720082539),
('/elfinder/*', 2, NULL, NULL, NULL, 1720082544, 1720082544),
('/gii/*', 2, NULL, NULL, NULL, 1720082542, 1720082542),
('/profile/*', 2, NULL, NULL, NULL, 1720082559, 1720082559),
('/rbac/*', 2, NULL, NULL, NULL, 1720082535, 1720082535),
('/site/finish-master', 2, NULL, NULL, NULL, 1720082572, 1720082572),
('admin', 1, NULL, NULL, NULL, 1720082369, 1720082369),
('client', 1, NULL, NULL, NULL, 1720082459, 1720082459),
('master', 1, NULL, NULL, NULL, 1720082452, 1720082452);

-- --------------------------------------------------------

--
-- Структура таблицы `auth_item_child`
--

CREATE TABLE `auth_item_child` (
  `parent` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `child` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Дамп данных таблицы `auth_item_child`
--

INSERT INTO `auth_item_child` (`parent`, `child`) VALUES
('admin', '/admin/*'),
('admin', '/debug/*'),
('admin', '/elfinder/*'),
('admin', '/gii/*'),
('client', '/profile/*'),
('admin', '/rbac/*'),
('master', '/site/finish-master'),
('admin', 'client'),
('master', 'client'),
('admin', 'master');

-- --------------------------------------------------------

--
-- Структура таблицы `auth_rule`
--

CREATE TABLE `auth_rule` (
  `name` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `data` blob,
  `created_at` int DEFAULT NULL,
  `updated_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `category`
--

CREATE TABLE `category` (
  `id` int NOT NULL,
  `active` int NOT NULL DEFAULT '1',
  `name` varchar(255) NOT NULL,
  `color` varchar(255) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL COMMENT 'Путь к изображению (для совместимости)',
  `image_id` int DEFAULT NULL COMMENT 'ID изображения из таблицы image'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `category`
--

INSERT INTO `category` (`id`, `active`, `name`, `color`, `image_path`, `image_id`) VALUES
(1, 1, 'Макияж', '#a9d0ed', '/images/storage/image-by-item-and-alias.png', 14),
(2, 1, 'Ресницы', '#bdb7eb', '/images/storage/image-by-item-and-aflias.png', 15),
(3, 1, 'Ногти', '#e6b6ba', NULL, NULL),
(4, 1, 'Волосы', '#ffc897', NULL, NULL),
(5, 1, 'Массаж', '#dfb0fd', NULL, NULL),
(6, 1, 'Брови', '#aad09f', NULL, NULL),
(7, 1, 'Перманент', '#97aecf', NULL, NULL),
(8, 1, 'Эпиляция', '#fe8ccd', NULL, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `coupon`
--

CREATE TABLE `coupon` (
  `id` int NOT NULL,
  `category_id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `price` decimal(15,2) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL COMMENT 'Путь к изображению (для совместимости)',
  `image_id` int DEFAULT NULL COMMENT 'ID изображения из таблицы image'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `coupon`
--

INSERT INTO `coupon` (`id`, `category_id`, `name`, `description`, `amount`, `price`, `image_path`, `image_id`) VALUES
(3, 1, 'Купон на услугу', 'Дневной макияж', 500.00, 99.00, 'images/coupons/image-by-item-and.png', NULL),
(4, 1, 'Купон на услугу', 'Вечерний макияж', 500.00, 99.00, NULL, NULL),
(5, 1, 'Купон на услугу', 'Макияж на фотосессию', 500.00, 99.00, NULL, NULL),
(6, 1, 'Купон на услугу', 'Макияж на выпускной', 500.00, 99.00, NULL, NULL),
(7, 1, 'Купон на услугу', 'Пробный макияж', 500.00, 99.00, NULL, NULL),
(8, 1, 'Купон на услугу', 'Свадебный макияж', 500.00, 99.00, NULL, NULL),
(9, 1, 'Купон на услугу', '2в1: пробный макияж и свадебный макияж', 199.00, 1000.00, NULL, NULL),
(10, 6, 'Купон на услугу', 'Окрашивание + оформление бровей', 300.00, 99.00, NULL, NULL),
(11, 2, 'Купон на услугу', 'Наращивание ресниц: Классика', 500.00, 99.00, NULL, NULL),
(12, 2, 'Купон на услугу', 'Наращивания ресниц: 2D', 500.00, 99.00, NULL, NULL),
(13, 2, 'Купон на услугу', 'Наращивания ресниц: 3D', 500.00, 99.00, NULL, NULL),
(14, 2, 'Купон на услугу', 'Наращивания ресниц: 4D', 500.00, 99.00, NULL, NULL),
(15, 4, 'Купон на услугу', 'Укладка локоны', 500.00, 99.00, NULL, NULL),
(16, 4, 'Купон на услугу', 'Укладка на брашинг', 500.00, 99.00, NULL, NULL),
(17, 4, 'Купон на услугу', 'Окрашивание корней и тонирование волос (до плеч)', 500.00, 99.00, NULL, NULL),
(18, 4, 'Купон на услугу', 'Окрашивание корней и тонирование волос (до лопаток)', 500.00, 99.00, NULL, NULL),
(19, 4, 'Купон на услугу', 'Окрашивание корней и тонирование волос (ниже лопаток)', 500.00, 99.00, NULL, NULL),
(20, 4, 'Купон на услугу', 'Окрашивание в сложных техниках (AirTouch, мелирование) длина волос до плеч', 500.00, 99.00, NULL, NULL),
(21, 4, 'Купон на услугу', 'Окрашивание в сложных техниках (AirTouch, мелирование) длина волос до лопаток', 500.00, 99.00, NULL, NULL),
(22, 4, 'Купон на услугу', 'Окрашивание в сложных техниках (AirTouch, мелирование) длина волос ниже лопаток', 500.00, 99.00, NULL, NULL),
(23, 4, 'Купон на услугу', 'Тонирование по длине (волосы до плеч)', 500.00, 99.00, NULL, NULL),
(24, 4, 'Купон на услугу', 'Тонирование по длине (волосы до лопаток)', 500.00, 99.00, NULL, NULL),
(25, 4, 'Купон на услугу', 'Тонирование по длине (волосы ниже лопаток)', 500.00, 99.00, NULL, NULL),
(26, 4, 'Купон на услугу', '2в1: стрижка и укладка', 1000.00, 199.00, NULL, NULL),
(27, 3, 'Купон на услугу', 'Маникюр + покрытие гель лак (снятие в подарок)', 300.00, 99.00, NULL, NULL),
(28, 7, 'Купон на услугу', 'Перманентный макияж бровей', 1000.00, 199.00, NULL, NULL),
(29, 8, 'Купон на услугу', '3в1: подмышечные впадины + глубокое бикини + голени или бедра (сахар)', 500.00, 99.00, NULL, NULL),
(30, 5, 'Купон на услугу', 'Спортивный массаж (60 мин)', 500.00, 99.00, NULL, NULL),
(31, 5, 'Купон на услугу', 'Классический массаж (60 мин)', 500.00, 99.00, NULL, NULL),
(32, 5, 'Купон на услугу', 'Лимфодренажный (60 мин)', 500.00, 99.00, NULL, NULL),
(33, 5, 'Купон на услугу', 'Антицеллюлитный (60 мин)', 500.00, 99.00, NULL, NULL),
(34, 5, 'Купон на услугу', 'Массаж спины, шейно – воротниковой зоны (30 мин)', 500.00, 99.00, NULL, NULL),
(35, 5, 'Купон на услугу', 'Массаж лица «Миофасциальный» (60 мин)', 500.00, 99.00, NULL, NULL),
(36, 5, 'Купон на услугу', 'Классический массаж лица (60 мин)', 500.00, 99.00, NULL, NULL),
(37, 5, 'Купон на услугу', 'Скульптурно – буккальный массаж (60 мин)', 500.00, 99.00, NULL, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `files`
--

CREATE TABLE `files` (
  `id` int NOT NULL,
  `item_id` int NOT NULL,
  `type` int NOT NULL,
  `path` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `image`
--

CREATE TABLE `image` (
  `id` int NOT NULL,
  `filePath` varchar(400) NOT NULL,
  `itemId` int DEFAULT NULL,
  `isMain` tinyint(1) DEFAULT NULL,
  `modelName` varchar(150) NOT NULL,
  `urlAlias` varchar(400) NOT NULL,
  `name` varchar(80) DEFAULT NULL,
  `sorted` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `image`
--

INSERT INTO `image` (`id`, `filePath`, `itemId`, `isMain`, `modelName`, `urlAlias`, `name`, `sorted`) VALUES
(1, 'Categories/Category1/0d3fe1.png', 1, NULL, 'Category', '02ba5854da-1', '', 0),
(2, 'Categories/Category2/0069e1.png', 2, NULL, 'Category', '0e1c75f7cc-1', '', 0),
(3, 'Categories/Category3/615eb8.png', 3, NULL, 'Category', 'ced7e7f6b3-1', '', 0),
(4, 'Categories/Category4/112736.png', 4, NULL, 'Category', 'e3588f0c0d-1', '', 0),
(5, 'Categories/Category5/2a550c.png', 5, NULL, 'Category', 'adbd249d1b-1', '', 0),
(6, 'Categories/Category6/e5daab.png', 6, NULL, 'Category', 'd58a35b140-1', '', 0),
(7, 'Categories/Category7/b0afb6.png', 7, NULL, 'Category', '7b02ce7502-1', '', 0),
(8, 'Categories/Category8/cb0158.png', 8, NULL, 'Category', 'e877090f10-1', '', 0),
(10, 'Coupons/Coupon1/58567c.png', 1, NULL, 'Coupon', '5fb969c6f4-1', '', 0),
(12, 'Coupons/Coupon2/00d8f4.png', 2, NULL, 'Coupon', '6b8ddefe48-1', '', 0),
(14, '/images/storage/image-by-item-and-alias.png', 1, 1, 'Category', 'image-by-item-and-alias.png', 'image-by-item-and-alias.png', 0),
(15, '/images/storage/image-by-item-and-aflias.png', 2, 1, 'Category', 'image-by-item-and-aflias.png', 'image-by-item-and-aflias.png', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `master`
--

CREATE TABLE `master` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `lastname` varchar(255) DEFAULT NULL,
  `firstname` varchar(255) NOT NULL,
  `middlename` varchar(255) NOT NULL,
  `gender` int NOT NULL DEFAULT '0',
  `birthday` int NOT NULL,
  `date` int NOT NULL,
  `search_radius` int NOT NULL DEFAULT '0',
  `client_gender` int NOT NULL DEFAULT '0',
  `status` int NOT NULL DEFAULT '0',
  `work_lat` float DEFAULT NULL,
  `work_lon` float DEFAULT NULL,
  `live_lat` float DEFAULT NULL,
  `live_lon` float DEFAULT NULL,
  `work_city` varchar(255) DEFAULT NULL,
  `work_street` varchar(255) DEFAULT NULL,
  `work_house` varchar(255) DEFAULT NULL,
  `live_city` varchar(255) DEFAULT NULL,
  `live_street` varchar(255) DEFAULT NULL,
  `live_house` varchar(255) DEFAULT NULL,
  `order_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `master`
--

INSERT INTO `master` (`id`, `user_id`, `balance`, `lastname`, `firstname`, `middlename`, `gender`, `birthday`, `date`, `search_radius`, `client_gender`, `status`, `work_lat`, `work_lon`, `live_lat`, `live_lon`, `work_city`, `work_street`, `work_house`, `live_city`, `live_street`, `live_house`, `order_id`) VALUES
(1, 3, 0.00, 'fsdfs', 'пвапвапв', 'fdsfs', 1, 1752613200, 1752497640, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `migration`
--

CREATE TABLE `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `migration`
--

INSERT INTO `migration` (`version`, `apply_time`) VALUES
('m000000_000000_base', 1751913043);

-- --------------------------------------------------------

--
-- Структура таблицы `orders`
--

CREATE TABLE `orders` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `price` decimal(15,2) NOT NULL,
  `date` int NOT NULL,
  `info` int NOT NULL,
  `type` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `price`, `date`, `info`, `type`) VALUES
(1, NULL, 99.00, 1720087477, 7, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `order_application`
--

CREATE TABLE `order_application` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `phone` bigint NOT NULL,
  `date` int NOT NULL,
  `city` text NOT NULL,
  `street` varchar(255) NOT NULL,
  `house` varchar(255) NOT NULL,
  `apartment` varchar(255) NOT NULL,
  `entrance` varchar(255) NOT NULL,
  `floor` varchar(255) NOT NULL,
  `intercom` varchar(255) NOT NULL,
  `time` int NOT NULL,
  `price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `order_coupon_id` int DEFAULT NULL,
  `lat` float NOT NULL,
  `lon` float NOT NULL,
  `product_id` int DEFAULT NULL,
  `comment` text NOT NULL,
  `master_id` int DEFAULT NULL,
  `status` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `order_application`
--

INSERT INTO `order_application` (`id`, `user_id`, `name`, `phone`, `date`, `city`, `street`, `house`, `apartment`, `entrance`, `floor`, `intercom`, `time`, `price`, `order_coupon_id`, `lat`, `lon`, `product_id`, `comment`, `master_id`, `status`) VALUES
(2, NULL, 'ильяс', 79991890133, 1720040400, 'г Ижевск ', 'ул Пушкинская', 'д 304', '3', '3', '4', '2', 15, 0.00, NULL, 56.8501, 53.2126, NULL, '', NULL, 0),
(3, NULL, 'ильяс', 78999189013, 1720040400, 'г Ижевск', 'ул Кавказская', 'д 3 ', '3', '13', '4', '4', 14, 99.00, NULL, 56.8031, 53.1467, NULL, '', NULL, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `order_coupon`
--

CREATE TABLE `order_coupon` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `coupon_id` int DEFAULT NULL,
  `price` decimal(15,2) NOT NULL,
  `date` int NOT NULL,
  `order_id` varchar(255) DEFAULT NULL,
  `status` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `order_coupon`
--

INSERT INTO `order_coupon` (`id`, `user_id`, `coupon_id`, `price`, `date`, `order_id`, `status`) VALUES
(1, NULL, NULL, 99.00, 1720086329, NULL, 0),
(2, NULL, NULL, 99.00, 1720086345, 'COUPON_2', 0),
(3, NULL, NULL, 99.00, 1720086378, NULL, 0),
(4, NULL, NULL, 99.00, 1720086414, 'COUPON_4', 0),
(5, NULL, NULL, 99.00, 1720086454, NULL, 0),
(6, NULL, NULL, 99.00, 1720086586, '17200865861327', 0),
(7, NULL, NULL, 99.00, 1720087477, NULL, 0),
(8, 1, 3, 99.00, 1752498724, 'test_1752498724', 0),
(9, 1, 4, 99.00, 1752498769, 'test_1752498769', 0),
(10, 1, 5, 99.00, 1752498772, 'test_1752498772', 0),
(11, 1, 6, 99.00, 1752498776, 'test_1752498776', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `product`
--

CREATE TABLE `product` (
  `id` int NOT NULL,
  `category_id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `product`
--

INSERT INTO `product` (`id`, `category_id`, `name`, `price`) VALUES
(3, 1, 'Дневной макияж', 2900.00),
(4, 1, 'Вечерний макияж', 3900.00),
(5, 1, 'Макияж на фотосессию', 3900.00),
(6, 1, 'Макияж на выпускной', 3900.00),
(7, 1, 'Пробный макияж', 3500.00),
(8, 1, 'Свадебный макияж', 4400.00),
(9, 1, '2в1: пробный макияж и свадебный макияж', 7900.00),
(10, 6, 'Оформление бровей', 1000.00),
(11, 6, 'Окрашивание краска/хна', 1500.00),
(12, 6, 'Окрашивание + оформление бровей', 2000.00),
(13, 6, 'Долговременная укладка бровей (без оформления и окрашивания)', 2000.00),
(14, 6, 'Долговременная укладка бровей (включает в себя оформление и окрашивание)', 2800.00),
(15, 6, 'Мужское оформление бровей', 1500.00),
(16, 2, 'Наращивание ресниц: Классика', 3000.00),
(17, 2, 'Наращивания ресниц: 2D', 3500.00),
(18, 2, 'Наращивания ресниц: 3D', 4000.00),
(19, 2, 'Наращивания ресниц: 4D', 4500.00),
(20, 2, 'Ламинирование ресниц', 2500.00),
(21, 4, 'Стрижка любой сложности', 2000.00),
(22, 4, 'Стрижка любой сложности во время окрашивания', 1500.00),
(23, 4, 'Стрижка челки', 2000.00),
(24, 4, 'Стрижка кончиков', 1500.00),
(25, 1, 'Укладка локоны', 3000.00),
(26, 4, 'Укладка на брашинг', 3000.00),
(27, 4, 'Собранная причёска', 4500.00),
(28, 4, 'Плетение кос', 3000.00),
(29, 4, 'Окрашивание корней и тонирование волос (до плеч)', 6000.00),
(30, 4, 'Окрашивание корней и тонирование волос (до лопаток)', 7000.00),
(31, 4, 'Окрашивание корней и тонирование волос (ниже лопаток)', 8000.00),
(32, 4, 'Окрашивание корней до 4 см', 4500.00),
(33, 4, 'Окрашивание в сложных техниках (AirTouch, мелирование) длина волос до плеч', 8500.00),
(34, 4, 'Окрашивание в сложных техниках (AirTouch, мелирование) длина волос до лопаток', 9500.00),
(35, 4, 'Окрашивание в сложных техниках (AirTouch, мелирование) длина волос ниже лопаток', 10500.00),
(36, 4, 'Тонирование по длине (волосы до плеч)', 5000.00),
(37, 4, 'Тонирование по длине (волосы до лопаток)', 6000.00),
(38, 4, 'Тонирование по длине (волосы ниже лопаток)', 7000.00),
(39, 4, '2в1: стрижка и укладка', 5000.00),
(40, 3, 'Маникюр + покрытие гель лак (снятие в подарок)', 2000.00),
(41, 7, 'Перманентный макияж бровей', 11000.00),
(42, 8, 'Подмышечные впадины (сахар)', 500.00),
(43, 8, '3в1: подмышечные впадины + глубокое бикини + голени или бедра (сахар)', 3100.00),
(44, 5, 'Спортивный массаж (60 мин)', 2800.00),
(45, 5, 'Классический массаж (60 мин)', 2000.00),
(46, 5, 'Лимфодренажный (60 мин)', 2300.00),
(47, 5, 'Антицеллюлитный (60 мин)', 2500.00),
(48, 5, 'Массаж спины, шейно – воротниковой зоны (30 мин)', 1300.00),
(49, 5, 'Массаж лица «Миофасциальный» (60 мин)', 1800.00),
(50, 5, 'Классический массаж лица (60 мин)', 1800.00),
(51, 5, 'Скульптурно – буккальный массаж (60 мин)', 1800.00);

-- --------------------------------------------------------

--
-- Структура таблицы `user`
--

CREATE TABLE `user` (
  `id` int NOT NULL,
  `phone` bigint NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `fcm_token` varchar(255) DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `user`
--

INSERT INTO `user` (`id`, `phone`, `name`, `email`, `fcm_token`, `token`, `password`) VALUES
(1, 73433232323, 'Peter', 'peter@mail.com', NULL, 'onkM5f1EKNNEI5ooMKQEEavjoNRvoYvV', '$2y$13$XUFvqP8sMQZbs6oflb5K2O1f8fRQQCiknkfvS7Z4vAlI.QSmFRuUK'),
(2, 74343434343, 'Dima', 'dima@mail.com', NULL, 'FU8UkH_Ukl88qCm2QP0uvyxncvtUCIH6', '$2y$13$zfv6tOa0HBfCXVFRrIwX3eMGuTMtroj2R46q7AT2cIIYnRx/HAkTe'),
(3, 78423423434, 'fdsfs', 'fsfsd@mail.com', NULL, 'l0kCEmXOYWoygkQzbl8tYXCKnt5gYyA2', '$2y$13$d9.k1gdZAgkS67Wv5AzWN.hUhPFAmU2Zr8ElV7dcZguzNuuD2WFtK');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `auth_assignment`
--
ALTER TABLE `auth_assignment`
  ADD PRIMARY KEY (`item_name`,`user_id`),
  ADD KEY `idx-auth_assignment-user_id` (`user_id`);

--
-- Индексы таблицы `auth_code`
--
ALTER TABLE `auth_code`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `auth_item`
--
ALTER TABLE `auth_item`
  ADD PRIMARY KEY (`name`),
  ADD KEY `rule_name` (`rule_name`),
  ADD KEY `idx-auth_item-type` (`type`);

--
-- Индексы таблицы `auth_item_child`
--
ALTER TABLE `auth_item_child`
  ADD PRIMARY KEY (`parent`,`child`),
  ADD KEY `child` (`child`);

--
-- Индексы таблицы `auth_rule`
--
ALTER TABLE `auth_rule`
  ADD PRIMARY KEY (`name`);

--
-- Индексы таблицы `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `coupon`
--
ALTER TABLE `coupon`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Индексы таблицы `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `image`
--
ALTER TABLE `image`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `master`
--
ALTER TABLE `master`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `migration`
--
ALTER TABLE `migration`
  ADD PRIMARY KEY (`version`);

--
-- Индексы таблицы `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `order_application`
--
ALTER TABLE `order_application`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `master_id` (`master_id`),
  ADD KEY `order_coupon_id` (`order_coupon_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Индексы таблицы `order_coupon`
--
ALTER TABLE `order_coupon`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `coupon_id` (`coupon_id`);

--
-- Индексы таблицы `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Индексы таблицы `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `auth_code`
--
ALTER TABLE `auth_code`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=309;

--
-- AUTO_INCREMENT для таблицы `category`
--
ALTER TABLE `category`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT для таблицы `coupon`
--
ALTER TABLE `coupon`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT для таблицы `files`
--
ALTER TABLE `files`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `image`
--
ALTER TABLE `image`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT для таблицы `master`
--
ALTER TABLE `master`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `order_application`
--
ALTER TABLE `order_application`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `order_coupon`
--
ALTER TABLE `order_coupon`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT для таблицы `product`
--
ALTER TABLE `product`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT для таблицы `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `auth_assignment`
--
ALTER TABLE `auth_assignment`
  ADD CONSTRAINT `auth_assignment_ibfk_1` FOREIGN KEY (`item_name`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `auth_code`
--
ALTER TABLE `auth_code`
  ADD CONSTRAINT `auth_code_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `auth_item`
--
ALTER TABLE `auth_item`
  ADD CONSTRAINT `auth_item_ibfk_1` FOREIGN KEY (`rule_name`) REFERENCES `auth_rule` (`name`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `auth_item_child`
--
ALTER TABLE `auth_item_child`
  ADD CONSTRAINT `auth_item_child_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `auth_item_child_ibfk_2` FOREIGN KEY (`child`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `coupon`
--
ALTER TABLE `coupon`
  ADD CONSTRAINT `coupon_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `master`
--
ALTER TABLE `master`
  ADD CONSTRAINT `master_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Ограничения внешнего ключа таблицы `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Ограничения внешнего ключа таблицы `order_application`
--
ALTER TABLE `order_application`
  ADD CONSTRAINT `order_application_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `order_application_ibfk_2` FOREIGN KEY (`master_id`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `order_application_ibfk_3` FOREIGN KEY (`order_coupon_id`) REFERENCES `order_coupon` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `order_application_ibfk_4` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Ограничения внешнего ключа таблицы `order_coupon`
--
ALTER TABLE `order_coupon`
  ADD CONSTRAINT `order_coupon_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `order_coupon_ibfk_2` FOREIGN KEY (`coupon_id`) REFERENCES `coupon` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Ограничения внешнего ключа таблицы `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `product_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
