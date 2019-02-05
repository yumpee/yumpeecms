-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 04, 2019 at 04:09 AM
-- Server version: 5.7.14
-- PHP Version: 7.0.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `yumpee_default`
--

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

CREATE TABLE `message` (
  `id` int(10) UNSIGNED NOT NULL,
  `language` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `translation` text COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sourcemessage`
--

CREATE TABLE `sourcemessage` (
  `id` int(10) UNSIGNED NOT NULL,
  `category` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `message` text COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_access_tokens`
--

CREATE TABLE `tbl_access_tokens` (
  `id` int(11) NOT NULL,
  `token` varchar(300) COLLATE utf8_unicode_ci NOT NULL,
  `expires_at` int(11) NOT NULL,
  `auth_code` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `app_id` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_articles`
--

CREATE TABLE `tbl_articles` (
  `id` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `url` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `article_type` tinyint(4) NOT NULL DEFAULT '1',
  `lead_content` text COLLATE utf8_unicode_ci NOT NULL,
  `body_content` text COLLATE utf8_unicode_ci NOT NULL,
  `featured_media` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `render_template` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `feedback` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date` date NOT NULL,
  `archive` int(11) DEFAULT NULL,
  `display_image_id` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `thumbnail_image_id` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `master_content` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `published` tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
  `updated` datetime DEFAULT NULL,
  `alternate_header_content` text COLLATE utf8_unicode_ci,
  `show_header_image` tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
  `published_by_stat` tinyint(4) NOT NULL,
  `usrname` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `require_login` char(1) COLLATE utf8_unicode_ci DEFAULT 'N',
  `disable_comments` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `rating` float DEFAULT NULL,
  `no_of_views` int(11) DEFAULT NULL,
  `sort_order` int(11) DEFAULT NULL,
  `permissions` text COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `tbl_articles`
--

INSERT INTO `tbl_articles` (`id`, `url`, `title`, `article_type`, `lead_content`, `body_content`, `featured_media`, `render_template`, `feedback`, `date`, `archive`, `display_image_id`, `thumbnail_image_id`, `master_content`, `published`, `updated`, `alternate_header_content`, `show_header_image`, `published_by_stat`, `usrname`, `require_login`, `disable_comments`, `rating`, `no_of_views`, `sort_order`, `permissions`) VALUES
('4188c92e74d51fe8ac01e6f7d6051e7a', 'cms-article', 'CMS Article', 1, '<p>The US Centers for Medicare&nbsp;</p>', '<p>The US Centers for Medicare &amp; Medicaid Services (CMS) has released an online tool that enables consumers to compare Medicare payments and copayments for 114 procedures that are performed in both hospital outpatient departments (HOPDs) and ambulatory surgical centers (ASCs).</p>\r\n<p>Required by the 21st Century Cures Act, the Procedure Price Lookup tool displays national averages of the amounts Medicare pays hospitals and ASCs and the national average copayment a Medicare beneficiary with no Medigap insurance would pay.</p>\r\n<p>For example, a Medicare patient might want to know how much CMS would pay a hospital and an ASC and what part of that he\'d have to pay, on average, for electroremoval of the prostate through bladder canal (urethra) with control of bleeding using an endoscope. The person would enter "prostate" into the tool, select this procedure from a drop-down list, and see the average Medicare cost and the average patient cost in both care settings. For this particular procedure, the patient would pay $351 in an ASC and $741 in an HOPD. The cost to Medicare would be more than twice as high in the HOPD as in the ASC.</p>\r\n<p>In a blog post, CMS Administrator Seema Verma said, "Procedure Price Lookup will help patients with Medicare consider potential cost differences when choosing among safe and clinically appropriate settings to get the care that best meets their needs."</p>\r\n<p>The Procedure Price Lookup is needed, she said, because the CMS is required by law to pay HOPDs differently than it pays ASCs. As a result, the CMS and Medicare beneficiaries pay "vastly different amounts for the same service, depending on the site of care." She noted that although it would take Congress to change the law, CMS can now provide some transparency to patients under the 21st Century Cures Act.</p>\r\n<p><strong>Impact on Physicians</strong></p>\r\n<p>Depending on how many consumers use Procedure Price Lookup and whether the price differential prompts more of them to select ASCs, this new online tool could affect physicians in at least two ways. First, patients might ask about the price comparison when they discuss elective surgery with their physicians. Second, physicians who co-own ACSs might benefit if more patients choose to have their procedures performed in ASCs.</p>\r\n<p>A few years ago, hospitals had an incentive to acquire surgical practices in order to capture the higher payments for procedures in HOPDs. But in 2016, CMS stopped classifying certain employed physicians with off-campus ambulatory practices as part of an HOPD. At the same time, it began requiring "site-neutral" Medicare payments to ambulatory care practices owned by hospitals. As a result, hospitals received lower payments for those doctors\' services than before, because the physicians were paid under the Medicare fee schedule.</p>\r\n<p>Procedure Price Lookup is the latest in a series of patient-oriented transparency tools from CMS. For example, CMS recently overhauled its drug pricing and spending dashboards, vastly increasing the number of medications that patients can look up. The agency has also enhanced its interactive online decision support feature to help people better understand and evaluate their Medicare coverage options. And CMS now offers a mobile-optimized out-of-pocket cost calculator to provide beneficiaries with information on overall plan and drug costs.</p>', '', '83ebb876a836e3e9ca2ecdf74b236809', '', '2018-11-29', 112018, '7f93f664be067f94f6f662222c9f5cf39069', '', 1, 1, '2018-11-29 00:03:23', NULL, 1, 0, 'admin', 'N', 'N', 0, 20, NULL, NULL),
('b660a897d58b8fb82249ecbf94c9c3e1', 'cms-launches-tool-that-prices-surgeries-for-patien', 'CMS launches tool', 1, '<p>The Centers for Medicare and Medicaid Services</p>', '<p>Nov. 27 (UPI) -- The Centers for Medicare and Medicaid Services, or CMS, on Tuesday unveiled a new online tool that gives consumers the power to compare Medicare payouts for various hospital procedures.</p>\r\n<p>The Procedure Price Lookup tool allows users to see the national averages for what Medicare pays to hospitals or ambulatory surgical centers, along with the national average copayment amounts that beneficiaries without Medicare supplemental insurance would pay the provider.</p>\r\n<p>"We must do something about rising cost, and a key pillar is to empower patients with the information they need to drive cost and quality by making our healthcare system evolve to one that competes for patients," Seema Verma, administrator of CMS, wrote in a blog post.</p>\r\n<p>The launch of the lookup tool comes almost two years after the passage of the 21st Century Cures Act to help sort out the difference in what Medicare patients and CMS pay for the same service.</p>\r\n<p>The rollout of the law is a part of CMS\' eMedicare initiative, designed to bring more overall price transparency to consumers who in need of prescription drugs and medical services.</p>\r\n<p>Included in the initiative are optimized drug pricing and spending dashboards, and a cost estimator for drugs and services not covered by Medicare.</p>\r\n<p>In the same spirit of transparency, President Trump also signed two bills into law in October removing "gag orders" that kept pharmacists from giving tips to customers on how to purchase cheaper prescription drugs. Later that month he floated a proposal to require pharmaceutical companies to disclose drug costs in television ads.</p>\r\n<p>"While the work we have done to empower patients by increasing the transparency of the Medicare program is unprecedented, we are just getting started as we work to increase price transparency throughout the healthcare system," Verma said.</p>', '', '83ebb876a836e3e9ca2ecdf74b236809', '', '2018-11-29', 112018, '7f93f664be067f94f6f662222c9f5cf31835', '', 1, 1, '2018-11-29 00:06:26', NULL, 1, 1, 'admin', 'N', 'N', 0, 6, NULL, NULL),
('dbfd606e6930a2512bff571b83a70f65', 'sample-article', 'Sample Article', 1, '<p>Sample Article with YumpeeCMS</p>', '<p>This is a sample article. Hope you have some fun building with Yumpee CMS.</p>\r\n<p>There are 13 different types of Page Template types in YumpeeCMS. A page template indicates how a<br />particular request will be routed and also how the information will be displayed. Every template type is<br />mapped to a specific route within the Yumpee framework.</p>\r\n<p><br />Within YumpeeCMS, you are also able to setup child templates from any of the core parent templates.<br />When a child template is setup, the system will route the URL call the same way it would have routed<br />the parent template but you now have the option of presenting the data in a different way. This means<br />that the same data can be presented in multiple page layouts using child templates. More of this will<br />be discussed in Chapter 12 &ndash; Extending Your Application</p>\r\n<p><em>An excerpt from Yumpee System Administrator Guide</em></p>', '', '83ebb876a836e3e9ca2ecdf74b236809', '', '2018-09-20', 92018, 'cf98487258357fddf6ceae69cd852a9c6359', '', 1, 1, '2018-11-22 23:08:52', NULL, 1, 1, 'admin', 'N', 'N', 0, 81, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_articles_blog_index`
--

CREATE TABLE `tbl_articles_blog_index` (
  `articles_id` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `blog_index_id` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_articles_blog_index`
--

INSERT INTO `tbl_articles_blog_index` (`articles_id`, `blog_index_id`) VALUES
('dbfd606e6930a2512bff571b83a70f65', 'fe43812374f05721712180a04eea8e42'),
('4188c92e74d51fe8ac01e6f7d6051e7a', 'fe43812374f05721712180a04eea8e42'),
('b660a897d58b8fb82249ecbf94c9c3e1', 'fe43812374f05721712180a04eea8e42');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_articles_category`
--

CREATE TABLE `tbl_articles_category` (
  `id` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `url` varchar(50) CHARACTER SET utf8 NOT NULL,
  `description` text CHARACTER SET utf8 NOT NULL,
  `display_order` int(10) UNSIGNED NOT NULL DEFAULT '100',
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `display_image_id` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `master_content` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `icon` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `tbl_articles_category`
--

INSERT INTO `tbl_articles_category` (`id`, `name`, `url`, `description`, `display_order`, `published`, `display_image_id`, `master_content`, `icon`) VALUES
('74280b9c3df37aa3a19d544de4f4f182', 'News', 'news', '', 1, 1, '', 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_articles_category_index`
--

CREATE TABLE `tbl_articles_category_index` (
  `category_id` varchar(50) NOT NULL,
  `category_index_id` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_articles_category_index`
--

INSERT INTO `tbl_articles_category_index` (`category_id`, `category_index_id`) VALUES
('74280b9c3df37aa3a19d544de4f4f182', 'd5f3d315b4ba211f11c5a06752898d1b');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_articles_category_related`
--

CREATE TABLE `tbl_articles_category_related` (
  `articles_id` varchar(50) NOT NULL,
  `category_id` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_articles_category_related`
--

INSERT INTO `tbl_articles_category_related` (`articles_id`, `category_id`) VALUES
('dbfd606e6930a2512bff571b83a70f65', '74280b9c3df37aa3a19d544de4f4f182'),
('4188c92e74d51fe8ac01e6f7d6051e7a', '74280b9c3df37aa3a19d544de4f4f182'),
('b660a897d58b8fb82249ecbf94c9c3e1', '74280b9c3df37aa3a19d544de4f4f182');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_articles_tag`
--

CREATE TABLE `tbl_articles_tag` (
  `articles_id` varchar(50) NOT NULL,
  `tags_id` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_article_details`
--

CREATE TABLE `tbl_article_details` (
  `id` int(11) NOT NULL,
  `article_id` varchar(36) NOT NULL,
  `param` varchar(250) NOT NULL,
  `param_val` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_article_files`
--

CREATE TABLE `tbl_article_files` (
  `id` varchar(40) DEFAULT NULL,
  `doc_name` varchar(200) DEFAULT NULL,
  `file_name` varchar(200) DEFAULT NULL,
  `file_path` varchar(200) DEFAULT NULL,
  `file_type` varchar(20) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_article_media`
--

CREATE TABLE `tbl_article_media` (
  `id` varchar(40) NOT NULL,
  `article_id` varchar(40) DEFAULT NULL,
  `media_id` varchar(200) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_authorization_codes`
--

CREATE TABLE `tbl_authorization_codes` (
  `id` int(11) NOT NULL,
  `code` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `expires_at` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `app_id` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_backend_menu`
--

CREATE TABLE `tbl_backend_menu` (
  `id` varchar(40) NOT NULL,
  `label` varchar(100) DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `url` varchar(200) DEFAULT NULL,
  `parent_id` varchar(40) DEFAULT NULL,
  `priority` int(11) DEFAULT NULL,
  `custom_stat` char(1) DEFAULT NULL,
  `original_label` varchar(100) DEFAULT NULL,
  `notes` text
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_backend_menu`
--

INSERT INTO `tbl_backend_menu` (`id`, `label`, `icon`, `url`, `parent_id`, `priority`, `custom_stat`, `original_label`, `notes`) VALUES
('648fed998ad5b91a50515d57ff101529', 'Blogs', 'fa fa-file', '#', '', 1, 'N', 'Blog', NULL),
('fe42bab6f882b1216f6fc2b63ca77110', 'Articles', 'fa fa-file-text', '/articles/index', '648fed998ad5b91a50515d57ff101529', 1, 'N', 'Articles', NULL),
('ccc84ebc211e5fa496122b2bdfb0a1cf', 'Category', 'fa fa-folder', '/articles/category', '36a7174121fc69513bafc88a7a2ef96a', 2, 'N', 'Category', NULL),
('a8cfbc4293b4c102ed4baaa54e0391bd', 'Tags', 'fa fa-tags', '/tags/index', '648fed998ad5b91a50515d57ff101529', 3, 'N', 'Tags', NULL),
('54221876ec47f53f098eb36c33c45add', 'Media', 'fa fa-file-image-o', '/media/index', '648fed998ad5b91a50515d57ff101529', 4, 'N', 'Media', NULL),
('dff9c412be1f9d0278832bb67d19b6b0', 'Comments', 'fa fa-comments', '/comment/index', '648fed998ad5b91a50515d57ff101529', 5, 'N', 'Comments', NULL),
('ece1b4b1a4e06c0135d147e8dec3c716', 'Testimonials', 'fa fa-quote-left', '/testimonials/index', '648fed998ad5b91a50515d57ff101529', 6, 'N', 'Testimonials', NULL),
('8d3d7eee9911ff4744f88f7c712b9ca3', 'Subscription', 'fa fa-users', '/subscriptions/index', '648fed998ad5b91a50515d57ff101529', 7, 'N', 'Subscription', NULL),
('36a7174121fc69513bafc88a7a2ef96a', 'Web', 'fa fa-globe', '#', '', 2, 'N', 'Web', NULL),
('ae92d2e80c54f20f40e8ec500a1df166', 'Blocks', 'fa fa-th-large', '/blocks/index', '36a7174121fc69513bafc88a7a2ef96a', 1, 'N', 'Blocks', NULL),
('b7f0644444488d48082928751566e4fd', 'Widgets', 'fa fa-th', '/widgets/index', '36a7174121fc69513bafc88a7a2ef96a', 2, 'N', 'Widgets', NULL),
('f0c0aabdb882cca77804935e6096e298', 'Pages', 'fa fa-file', '/pages/index', '36a7174121fc69513bafc88a7a2ef96a', 3, 'N', 'Pages', NULL),
('1ed90f32d3493af4f68c47241662235e', 'CSS Profiles', 'fa fa-code', '/css/index', '36a7174121fc69513bafc88a7a2ef96a', 4, 'N', 'CSS Profiles', NULL),
('d34f09bb7b66de57e4e9292e3a6f6195', 'Gallery', 'fa fa-image', '/gallery/index', '36a7174121fc69513bafc88a7a2ef96a', 5, 'N', 'Gallery', NULL),
('68c147aae5bd9f91869904498e27b670', 'Slider', 'fa fa-sliders', '/slider/index', '36a7174121fc69513bafc88a7a2ef96a', 6, 'N', 'Slider', NULL),
('60948185e9ed2aa0dfa22943763a9152', 'Rating Profiles', 'fa fa-star', '/rating/index', '36a7174121fc69513bafc88a7a2ef96a', 7, 'N', 'Rating Profiles', NULL),
('07357cbe582e0dd4d437b0ac7b2ea781', 'Menus', 'fa fa-th-list', '/menus/index', '36a7174121fc69513bafc88a7a2ef96a', 8, 'N', 'Menus', NULL),
('dd333a89e9c586ed0499ea745a426d1c', 'Templates', 'fa fa-globe', '/templates/index', '36a7174121fc69513bafc88a7a2ef96a', 9, 'N', 'Templates', NULL),
('9feaf2426bc90ce45b88d8d5ffa1dd05', 'Forms', 'fa fa-minus-square-o', '/forms/index', '36a7174121fc69513bafc88a7a2ef96a', 10, 'N', 'Forms', NULL),
('d8edeb960e5b05e127354f2e0e15f5d3', 'System', 'fa fa-cogs', '#', '', 4, 'N', 'System', NULL),
('73b815cf282cbd20456cdb2211586506', 'Users', 'fa fa-user', '/users/index', 'd8edeb960e5b05e127354f2e0e15f5d3', 1, 'N', 'Users', NULL),
('74f85f3d4d6ae6b7d3b3d7c1e4b3807b', 'Themes', 'fa fa-adjust', '/themes/index', 'd8edeb960e5b05e127354f2e0e15f5d3', 2, 'N', 'Themes', NULL),
('ba4c68ab464a4ef7d91d913e0c88d4d7', 'Settings', 'fa fa-cog', '/settings/index', 'd8edeb960e5b05e127354f2e0e15f5d3', 3, 'N', 'Settings', NULL),
('d27772585b6b3ef11be0bac7e939116c', 'Class Setup', 'fa fa-building', '/setup/index', 'd8edeb960e5b05e127354f2e0e15f5d3', 4, 'N', 'Class Setup', NULL),
('d60949991b5706c87032ce94551e09f2', 'Extensions', 'fa fa-code', '#', '', 5, 'N', 'Extensions', NULL),
('de630474ebd9d0b7055216892b35a009', 'Widgets', 'fa fa-th', '/widgets/extensions', 'd60949991b5706c87032ce94551e09f2', 1, 'N', 'Widgets', NULL),
('21c99aca0eca0fd5dab609c4aa8323ed', 'Views', 'fa fa-adjust', '/themes/extensions', 'd60949991b5706c87032ce94551e09f2', 2, 'N', 'Views', NULL),
('5d09d60ea0c68b83cd8e11f9e654fa5e', 'Forms', 'fa fa-minus-square-o', '#', 'd60949991b5706c87032ce94551e09f2', 3, 'N', 'Forms', NULL),
('98be1d014d1961bc8396247c6ead0a08', 'Post', 'fa fa-plus-circle', '/forms/extensions', '5d09d60ea0c68b83cd8e11f9e654fa5e', 1, 'N', 'Post', NULL),
('4d9e03cebf8db06726a8623b549c08a2', 'Summary View', 'fa fa-eye', '/forms/views', '5d09d60ea0c68b83cd8e11f9e654fa5e', 2, 'N', 'Summary View', NULL),
('6177d4ac5d0029963158be34ccf111c8', 'Details View', 'fa fa-list', '/forms/fdetails', '5d09d60ea0c68b83cd8e11f9e654fa5e', 3, 'N', 'Details View', NULL),
('4c2be5c9903fd8d347a819e02287d84a', 'Widgets', 'fa fa-windows', '/forms/fwidgets', '5d09d60ea0c68b83cd8e11f9e654fa5e', 4, 'N', 'Widgets', NULL),
('a592657bc3b23d6a7c0e583068a83d7f', 'Import', 'fa fa-file', '/themes/import', 'd60949991b5706c87032ce94551e09f2', 4, 'N', 'Import', NULL),
('311e9aaed8d59db431e547740f37cf24', 'Custom', '', '#', '', 6, 'Y', 'Custom', NULL),
('4a52aa14fb52d790a5619ddd4d85d21e', 'Setup', '', '#', '311e9aaed8d59db431e547740f37cf24', 1, 'Y', 'Setup', NULL),
('3548842d96013ecc2908bf0720594f3e', 'Form Data', '', '#', '311e9aaed8d59db431e547740f37cf24', 2, 'Y', 'Form Data', NULL),
('328261b83f5a4c39290c8c7ec219dbe7', 'Relationships', 'fa fa-sitemap', '/relationships/index', 'd60949991b5706c87032ce94551e09f2', 5, 'N', '', ''),
('2afecb06173de7f714ebaff034ef0a24', 'Translation', 'fa fa-language', '/translation/index', '36a7174121fc69513bafc88a7a2ef96a', 10, 'N', '', ''),
('b2d80a7911b9ef4c84a4f23b105ec865', 'Language', 'fa fa-language', '/language/index', 'd8edeb960e5b05e127354f2e0e15f5d3', 10, 'N', '', ''),
('32090ed63354d1ad42a179a7c82dca11', 'Theme Settings', 'fa fa-cog', '/themes/settings', 'd60949991b5706c87032ce94551e09f2', 6, 'N', NULL, NULL),
('2a461f42cdfd9d2a06502e2f5208a3fc', 'Reports', 'fa fa-bar-chart', '#', '', 7, 'N', 'Reports', NULL),
('4977b9d38f0270452a0bcd7d61483f3f', 'Setup', 'fa fa-cog', '/reports/setup', '2a461f42cdfd9d2a06502e2f5208a3fc', 1, 'N', 'Setup', NULL),
('81f3596e5693b235edf1bc0e662a10c2', 'Report List', 'fa fa-list', '/reports/list', '2a461f42cdfd9d2a06502e2f5208a3fc', 2, 'N', 'Report List', NULL),
('eea3666ff9c74d95c5ded8c127683a07', 'System Logs', 'fa fa-history', '/reports/logs', '2a461f42cdfd9d2a06502e2f5208a3fc', 3, 'N', 'System Logs', NULL),
('19749cd88d6cd37514c5fa76924d315b', 'Web Services', 'fa fa-plug', '#', '', 8, 'N', 'Web Services', NULL),
('96a4d6a5dcffc19319d236c22656e5f6', 'Client Profile', 'fa fa-user', '#', '19749cd88d6cd37514c5fa76924d315b', 1, 'N', 'Client Profile', NULL),
('e247e2979be02baee5456af9db9033a8', 'Incoming', 'fa fa-sign-in', '/services/incoming', '96a4d6a5dcffc19319d236c22656e5f6', 1, 'N', 'Incoming', NULL),
('7f517d19c08181212ab0d0ada28deb55', 'Outgoing', 'fa fa-sign-out', '/services/outgoing', '96a4d6a5dcffc19319d236c22656e5f6', 2, 'N', 'Outgoing', NULL),
('aec7d628c10ca4c4053c73a4c0b837ed', 'Resource Profile', 'fa fa-database', '/services/resource', '19749cd88d6cd37514c5fa76924d315b', 2, 'N', 'Resource Profile', NULL),
('a497028c7b51e3a1b2d8434637475023', 'Emulator', 'fa fa-rss', '/services/emulator', '19749cd88d6cd37514c5fa76924d315b', 3, 'N', 'Emulator', NULL),
('b41e79f0292332e1a1112560720bac72', 'Logs', 'fa fa-history', '/services/logs', '19749cd88d6cd37514c5fa76924d315b', 4, 'N', 'Logs', NULL),
('7053f1edec9c57d4aa0eb56c3bbc5b0f', 'Domains', 'fa fa-globe', '/domains/index', 'd8edeb960e5b05e127354f2e0e15f5d3', 5, 'N', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_backend_menu_role`
--

CREATE TABLE `tbl_backend_menu_role` (
  `id` varchar(40) NOT NULL,
  `menu_id` varchar(40) DEFAULT NULL,
  `role_id` varchar(40) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_backend_menu_role`
--

INSERT INTO `tbl_backend_menu_role` (`id`, `menu_id`, `role_id`) VALUES
('7df328342a082974bf6a37f8a0f35689', 'a8cfbc4293b4c102ed4baaa54e0391bd', 'f2d16e5cedb1c39a8630b775974565'),
('89420611e9131cc9d37d4f30bbaac4fa', 'eea3666ff9c74d95c5ded8c127683a07', 'f2d16e5cedb1c39a8630b775974565'),
('1d81aad2bad094455ee2face83f30f53', 'd8edeb960e5b05e127354f2e0e15f5d3', 'f2d16e5cedb1c39a8630b775974565'),
('41dbe0a236783b57cbec138f6e611282', '4d9e03cebf8db06726a8623b549c08a2', 'f2d16e5cedb1c39a8630b775974565'),
('3f1eecef6218dcfee80591b4ba7999cf', '8d3d7eee9911ff4744f88f7c712b9ca3', 'f2d16e5cedb1c39a8630b775974565'),
('f3e9ed5b8d550d2efb737714baf8cf3b', '68c147aae5bd9f91869904498e27b670', 'f2d16e5cedb1c39a8630b775974565'),
('d1a787dfce81fc317bb1158ae03f0ac6', '4977b9d38f0270452a0bcd7d61483f3f', 'f2d16e5cedb1c39a8630b775974565'),
('a4bd955efc0bf359e94038be4f8e3aea', '4a52aa14fb52d790a5619ddd4d85d21e', 'f2d16e5cedb1c39a8630b775974565'),
('019c27bcb594045ded08adbfdabec774', 'ba4c68ab464a4ef7d91d913e0c88d4d7', 'f2d16e5cedb1c39a8630b775974565'),
('f4a173ceb822a4b41853cdc936e65106', 'aec7d628c10ca4c4053c73a4c0b837ed', 'f2d16e5cedb1c39a8630b775974565'),
('27c385906e12bea00b1ced19598bfd0c', '2a461f42cdfd9d2a06502e2f5208a3fc', 'f2d16e5cedb1c39a8630b775974565'),
('a41f0747728298d3ee5a91a619075d31', '81f3596e5693b235edf1bc0e662a10c2', 'f2d16e5cedb1c39a8630b775974565'),
('1b0086b0af631ae24c00ab57407985f8', '328261b83f5a4c39290c8c7ec219dbe7', 'f2d16e5cedb1c39a8630b775974565'),
('16914b9e106e247bee33d1f3222c6c44', '60948185e9ed2aa0dfa22943763a9152', 'f2d16e5cedb1c39a8630b775974565'),
('6745a831ef05fc5e0da3a390ef83ef1f', '98be1d014d1961bc8396247c6ead0a08', 'f2d16e5cedb1c39a8630b775974565'),
('ca97cc760d573de7e9f581d0855c2daa', 'f0c0aabdb882cca77804935e6096e298', 'f2d16e5cedb1c39a8630b775974565'),
('591a4f614d677b5d79b52a28a30249ab', '7f517d19c08181212ab0d0ada28deb55', 'f2d16e5cedb1c39a8630b775974565'),
('a4ea62177585204ecb31edfa8c6eccb1', '07357cbe582e0dd4d437b0ac7b2ea781', 'f2d16e5cedb1c39a8630b775974565'),
('b5c149b1927aae5f35e7c0fef41fd435', '54221876ec47f53f098eb36c33c45add', 'f2d16e5cedb1c39a8630b775974565'),
('917e5d46281891b56308b087597eff91', 'b41e79f0292332e1a1112560720bac72', 'f2d16e5cedb1c39a8630b775974565'),
('46f257356d563e693d80c0a5f2fc14be', 'b2d80a7911b9ef4c84a4f23b105ec865', 'f2d16e5cedb1c39a8630b775974565'),
('2d3a1886546986174fc22124cc91fda3', '8d2bf3a467c7031eb283d9d40e67165d', 'f2d16e5cedb1c39a8630b775974565'),
('7097a34f233203a9443e0b481447888e', 'e247e2979be02baee5456af9db9033a8', 'f2d16e5cedb1c39a8630b775974565'),
('85ad996d56572fbd339354b83997be38', 'a592657bc3b23d6a7c0e583068a83d7f', 'f2d16e5cedb1c39a8630b775974565'),
('aef1066a7ca9d64d68741d9bfe556817', 'd34f09bb7b66de57e4e9292e3a6f6195', 'f2d16e5cedb1c39a8630b775974565'),
('13725670e30277f69bbe615d7260324b', '5d09d60ea0c68b83cd8e11f9e654fa5e', 'f2d16e5cedb1c39a8630b775974565'),
('fbffc60133390b829937dcd78dbd30f0', '9feaf2426bc90ce45b88d8d5ffa1dd05', 'f2d16e5cedb1c39a8630b775974565'),
('ada8c8b85ed77f659e844825c0b4c407', '3548842d96013ecc2908bf0720594f3e', 'f2d16e5cedb1c39a8630b775974565'),
('a9b76bf745eafef5457ed4e1ae552069', 'd60949991b5706c87032ce94551e09f2', 'f2d16e5cedb1c39a8630b775974565'),
('c02162bde804950b36b703a32a9d0040', 'a497028c7b51e3a1b2d8434637475023', 'f2d16e5cedb1c39a8630b775974565'),
('2e6095087c6a6066433a7d7147f5dc75', '6177d4ac5d0029963158be34ccf111c8', 'f2d16e5cedb1c39a8630b775974565'),
('da64d1572037d6ff5731bea1dcc9b69f', '311e9aaed8d59db431e547740f37cf24', 'f2d16e5cedb1c39a8630b775974565'),
('15528659cf4f3bbcfd5935420c692a8f', '1ed90f32d3493af4f68c47241662235e', 'f2d16e5cedb1c39a8630b775974565'),
('2adbf97c8c6d16240bd06340c07946fa', 'dff9c412be1f9d0278832bb67d19b6b0', 'f2d16e5cedb1c39a8630b775974565'),
('f0fce14590df403056b2595d78bf2f96', '96a4d6a5dcffc19319d236c22656e5f6', 'f2d16e5cedb1c39a8630b775974565'),
('21b15703b415a90efabb1aa342c5cd02', 'd27772585b6b3ef11be0bac7e939116c', 'f2d16e5cedb1c39a8630b775974565'),
('1ebb3628e86391a35307d8976e91cd3e', 'ccc84ebc211e5fa496122b2bdfb0a1cf', 'f2d16e5cedb1c39a8630b775974565'),
('ba80c86b33301e247dd5f2b888b207da', '648fed998ad5b91a50515d57ff101529', 'f2d16e5cedb1c39a8630b775974565'),
('ba164b463c38688f1115236ed9809a69', 'ae92d2e80c54f20f40e8ec500a1df166', 'f2d16e5cedb1c39a8630b775974565'),
('0935838204be219ef8b13588c117c4ad', 'fe42bab6f882b1216f6fc2b63ca77110', 'f2d16e5cedb1c39a8630b775974565'),
('bec5e56085c95ad028494ef29f8b027a', 'dd333a89e9c586ed0499ea745a426d1c', 'f2d16e5cedb1c39a8630b775974565'),
('ba95db9b0bcf438976e5f43d8e7df07f', 'ece1b4b1a4e06c0135d147e8dec3c716', 'f2d16e5cedb1c39a8630b775974565'),
('4a5cbe20e8c5c7f46ee3f3404ca2d576', '74f85f3d4d6ae6b7d3b3d7c1e4b3807b', 'f2d16e5cedb1c39a8630b775974565'),
('6216b879a597dbb1abe2b94a32b9afe3', '2afecb06173de7f714ebaff034ef0a24', 'f2d16e5cedb1c39a8630b775974565'),
('695fe7404dbd1cf82a230eaa359edae1', '73b815cf282cbd20456cdb2211586506', 'f2d16e5cedb1c39a8630b775974565'),
('597388e620d1d420b309db1342ad493e', '21c99aca0eca0fd5dab609c4aa8323ed', 'f2d16e5cedb1c39a8630b775974565'),
('7172e063eda4a9365991abcb882589f3', '36a7174121fc69513bafc88a7a2ef96a', 'f2d16e5cedb1c39a8630b775974565'),
('799fc3b9f7f7be5db939cb6e49c54cdb', '19749cd88d6cd37514c5fa76924d315b', 'f2d16e5cedb1c39a8630b775974565'),
('7c2f1d39e1b0813cd7c5396f59d85f56', 'b7f0644444488d48082928751566e4fd', 'f2d16e5cedb1c39a8630b775974565'),
('7217bf151f3a834393ca0caa6bccefd9', 'de630474ebd9d0b7055216892b35a009', 'f2d16e5cedb1c39a8630b775974565'),
('3fb32ad060f2394843f145841d1045df', '4c2be5c9903fd8d347a819e02287d84a', 'f2d16e5cedb1c39a8630b775974565');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_block`
--

CREATE TABLE `tbl_block` (
  `id` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `master_content` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  `position` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'left',
  `widget` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sort_order` int(3) UNSIGNED NOT NULL DEFAULT '100',
  `show_title` tinyint(1) NOT NULL DEFAULT '0',
  `title_level` int(1) UNSIGNED NOT NULL DEFAULT '2',
  `content_class` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `title_class` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `content_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `title_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `published` tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
  `editable` int(1) DEFAULT '1',
  `permissions` text COLLATE utf8_unicode_ci,
  `require_login` char(1) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `tbl_block`
--

INSERT INTO `tbl_block` (`id`, `master_content`, `name`, `title`, `content`, `position`, `widget`, `sort_order`, `show_title`, `title_level`, `content_class`, `title_class`, `content_id`, `title_id`, `published`, `editable`, `permissions`, `require_login`) VALUES
('82808175071416b980a3b7d429d90c9f', 1, 'Block-Bottom-Left', 'Block Left', '<p>This is a FREE Blog Extension based on YumpeeCMS framework. Use for advertising your goods and services. To make changes to this text, go to the Web-&gt;Blocks section of your Admin Panel.</p>', 'after_left', NULL, 100, 1, 1, NULL, NULL, NULL, NULL, 1, 1, NULL, NULL),
('d6dcf609cea16be319bc3197bc0eaef4', 1, 'Home-Page-Slider', 'Home Page Slider', '<div class="slider-section">\r\n<div id="demo" class="carousel slide" data-ride="carousel">\r\n<div class="carousel-inner">\r\n<div class="carousel-item active">\r\n<div class="overlay">&nbsp;</div>\r\n<img src="{yumpee_setting}website_image_url{/yumpee_setting}/mp6d6l4466n1udeh5ue7ns3nf1/15434463725bff1f64aae875-39042587.jpeg" alt="slide1" />\r\n<div class="carousel-caption">\r\n<h1 class="animated wow fadeInDown hero-heading animated" data-wow-delay=".4s">Welcome to YumpeeCMS Blog</h1>\r\n<p class="animated fadeInUp wow hero-sub-heading animated" data-wow-delay=".6s">Post and Share articles in an organised form</p>\r\n</div>\r\n</div>\r\n<div class="carousel-item">\r\n<div class="overlay">&nbsp;</div>\r\n<img src="{yumpee_setting}website_image_url{/yumpee_setting}/mp6d6l4466n1udeh5ue7ns3nf1/15434463725bff1f64bbc136-05482540.jpeg" alt="slide3" />\r\n<div class="carousel-caption">\r\n<h1 class="animated wow fadeInLeft hero-heading animated" data-wow-delay=".7s">Get People to respond to you post</h1>\r\n<p class="animated wow fadeInRight hero-sub-heading animated" data-wow-delay=".9s">Share your posts with your social media. Include your custom forms to get more custom responses</p>\r\n</div>\r\n</div>\r\n<div class="carousel-item"><img src="{yumpee_setting}website_image_url{/yumpee_setting}/mp6d6l4466n1udeh5ue7ns3nf1/15434463725bff1f64ca6764-16565506.jpeg" alt="slide3" />\r\n<div class="carousel-caption">\r\n<h1 class="animated wow fadeInDown hero-heading animated" data-wow-delay=".6s">Yes it is completely responsive</h1>\r\n<p class="animated fadeInUp wow hero-sub-heading animated" data-wow-delay=".8s">No additional work needed to have it display on all devices.</p>\r\n</div>\r\n</div>\r\n</div>\r\n<a id="qq" class="carousel-control-prev" href="#demo" data-slide="prev"> <span id="car1" class="carousel-control-prev-icon"></span> </a> <a id="bb" class="carousel-control-next" href="#demo" data-slide="next"> <span id="car2" class="carousel-control-next-icon"></span></a></div>\r\n</div>', 'before_content', NULL, 100, 1, 1, NULL, NULL, NULL, NULL, 1, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_block_group`
--

CREATE TABLE `tbl_block_group` (
  `id` varchar(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_block_group_list`
--

CREATE TABLE `tbl_block_group_list` (
  `id` varchar(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `block_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `group_id` varchar(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_block_group_list`
--

INSERT INTO `tbl_block_group_list` (`id`, `block_id`, `group_id`) VALUES
('06aec45fcda5cb8f685ae813926506ca', 'df3eff63aae94628a542690ef0bf5f0e', '0eaa4bcd0419924fc1b149835d6d3c3d');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_block_page`
--

CREATE TABLE `tbl_block_page` (
  `block_id` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `page_id` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `master_content` tinyint(1) DEFAULT NULL,
  `id` varchar(36) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `tbl_block_page`
--

INSERT INTO `tbl_block_page` (`block_id`, `page_id`, `master_content`, `id`) VALUES
('82808175071416b980a3b7d429d90c9f', 'fe43812374f05721712180a04eea8e42', 1, '044cee8156aa5977dc7ed1a54eb082d3'),
('0d3358d8ac3d7574d9bb65038c1a24a4', 'f0769958b0fba394ae53a0d261b445b9', 1, '0e64f864bb3218d41c3b1bcc88eb694a'),
('0d3358d8ac3d7574d9bb65038c1a24a4', '1cededebf55c6f73069ff7a2e8621eab', 1, '0f66580f36c65f829658b0c55d8ece27'),
('82808175071416b980a3b7d429d90c9f', 'f0769958b0fba394ae53a0d261b445b9', 1, '2f161fc71d09eb06ef12edf7a8cbcd27'),
('82808175071416b980a3b7d429d90c9f', '5e455ec9f9c9d21609d9d5345f2b890f', 1, '37f09b7a12ad034dfcbb43e220467f57'),
('0d3358d8ac3d7574d9bb65038c1a24a4', 'bd22a42ed957ffd7a171501aa7886b48', 1, '403840c8a2491b7624af6839d3120f9b'),
('0d3358d8ac3d7574d9bb65038c1a24a4', 'fe43812374f05721712180a04eea8e42', 1, '48bc44f547b3e51532317e5c063c8bf6'),
('82808175071416b980a3b7d429d90c9f', '1cededebf55c6f73069ff7a2e8621eab', 1, '576c3872d40b3549a30c78ae23a3ce3e'),
('82808175071416b980a3b7d429d90c9f', 'd5f3d315b4ba211f11c5a06752898d1b', 1, '5d78b07b52ad729e48b1d7c9a109b4e7'),
('0d3358d8ac3d7574d9bb65038c1a24a4', '9d78bca98209b62fb94ae6cf2ea01090', 1, '5dfe5dfd2ca17151be8e71076ff61438'),
('82808175071416b980a3b7d429d90c9f', 'dc5f6b778e4d259ba74892c76acc63dd', 1, '5fa26667101a4652480d3bda4da9a155'),
('82808175071416b980a3b7d429d90c9f', 'bb33302a6fe9d713db5307628d21996f', 1, '6063b9c8fb217bec5bbdab994f28426a'),
('0d3358d8ac3d7574d9bb65038c1a24a4', 'a5ca6c34-db38-11e3-a2bd-52540079a862', 1, '96018a6ea0690a243e06f77b59d24d2b'),
('0d3358d8ac3d7574d9bb65038c1a24a4', 'd5f3d315b4ba211f11c5a06752898d1b', 1, '98cc9901207347751a34beb739c2945f'),
('0d3358d8ac3d7574d9bb65038c1a24a4', 'bb33302a6fe9d713db5307628d21996f', 1, '9b2f96f80437f7c435985270ddbfab0d'),
('82808175071416b980a3b7d429d90c9f', 'bd22a42ed957ffd7a171501aa7886b48', 1, '9ef469a7ce78f29117d21968955af655'),
('82808175071416b980a3b7d429d90c9f', '9d78bca98209b62fb94ae6cf2ea01090', 1, 'afb9e7ba2230537d31af5cd0c9660894'),
('0d3358d8ac3d7574d9bb65038c1a24a4', 'dc5f6b778e4d259ba74892c76acc63dd', 1, 'c28585451cfba47d02915a6bef9386b8'),
('0d3358d8ac3d7574d9bb65038c1a24a4', '5e455ec9f9c9d21609d9d5345f2b890f', 1, 'd59ddc54052332b6a8a5fcf0d2924cf4'),
('82808175071416b980a3b7d429d90c9f', 'a5ca6c34-db38-11e3-a2bd-52540079a862', 1, 'ea335d344cad17c294f6b628d03ee612');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_categories`
--

CREATE TABLE `tbl_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_class_attributes`
--

CREATE TABLE `tbl_class_attributes` (
  `id` varchar(36) NOT NULL,
  `name` varchar(100) NOT NULL,
  `alias` varchar(250) NOT NULL,
  `class_id` varchar(36) NOT NULL,
  `parent_id` varchar(36) DEFAULT NULL,
  `description` text,
  `display_order` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_class_elements`
--

CREATE TABLE `tbl_class_elements` (
  `id` varchar(36) NOT NULL,
  `name` varchar(100) NOT NULL,
  `alias` varchar(250) NOT NULL,
  `class_id` varchar(36) NOT NULL,
  `parent_id` varchar(36) DEFAULT NULL,
  `description` text,
  `display_image_id` varchar(36) DEFAULT NULL,
  `display_order` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_class_elements_attributes`
--

CREATE TABLE `tbl_class_elements_attributes` (
  `id` varchar(36) NOT NULL,
  `element_id` varchar(36) NOT NULL,
  `attribute_id` varchar(36) NOT NULL,
  `element_attribute_val` text
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_class_setup`
--

CREATE TABLE `tbl_class_setup` (
  `id` varchar(36) NOT NULL,
  `name` varchar(100) NOT NULL,
  `alias` varchar(100) NOT NULL,
  `parent_id` varchar(36) DEFAULT NULL,
  `show_in_menu` char(1) NOT NULL DEFAULT 'Y',
  `display_image_id` varchar(36) DEFAULT NULL,
  `display_order` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_client_resources`
--

CREATE TABLE `tbl_client_resources` (
  `id` varchar(40) NOT NULL,
  `resource_id` varchar(40) DEFAULT NULL,
  `client_id` varchar(40) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_comments`
--

CREATE TABLE `tbl_comments` (
  `id` int(11) NOT NULL,
  `target_id` varchar(50) NOT NULL,
  `comment_type` char(15) NOT NULL,
  `author` varchar(50) DEFAULT NULL,
  `commentor` varchar(50) DEFAULT NULL,
  `comment` text NOT NULL,
  `date_commented` datetime DEFAULT NULL,
  `status` char(1) NOT NULL DEFAULT 'N',
  `ip_address` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `website` varchar(100) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_comments`
--

INSERT INTO `tbl_comments` (`id`, `target_id`, `comment_type`, `author`, `commentor`, `comment`, `date_commented`, `status`, `ip_address`, `email`, `website`, `parent_id`) VALUES
(13, '4188c92e74d51fe8ac01e6f7d6051e7a', 'article', 'admin', 'Admin', 'test', '2019-01-16 23:19:41', 'Y', '127.0.0.1', 'peter@audmaster.com', '', NULL),
(12, 'dbfd606e6930a2512bff571b83a70f65', 'article', NULL, 'test', 'This is a test comment', '2019-01-16 23:17:55', 'N', '127.0.0.1', 'peter@audmaster.com', '', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_css`
--

CREATE TABLE `tbl_css` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `css` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_custom_settings`
--

CREATE TABLE `tbl_custom_settings` (
  `id` varchar(40) NOT NULL,
  `setting_name` varchar(250) NOT NULL,
  `setting_value` text NOT NULL,
  `description` text NOT NULL,
  `theme_id` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_custom_settings`
--

INSERT INTO `tbl_custom_settings` (`id`, `setting_name`, `setting_value`, `description`, `theme_id`) VALUES
('d0e6720b5ea66391e0184a1d525d2b4f', 'custom_contact_address', '12 LaVista Place, QLD', '', NULL),
('ea4271d691fc151ad6620b6efe4b58ad', 'custom_support_phone_number', '12345', '', NULL),
('a2b36fae60b474a723092858ae7772b2', 'custom_support_email', 'support@mywebsite.com', '', NULL),
('bd8789b5c0a97503f1cfe55aa593e9dd', 'custom_copyright', 'Copyright 2017-2018 by YumpeeCMS', '', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_custom_widget`
--

CREATE TABLE `tbl_custom_widget` (
  `id` varchar(40) NOT NULL,
  `name` varchar(100) NOT NULL,
  `title` varchar(100) NOT NULL,
  `form_id` varchar(40) NOT NULL,
  `permissions` text,
  `require_login` char(1) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_domains`
--

CREATE TABLE `tbl_domains` (
  `id` varchar(40) NOT NULL,
  `name` varchar(250) DEFAULT NULL,
  `domain_url` varchar(250) DEFAULT NULL,
  `theme_id` int(11) DEFAULT NULL,
  `menu_id` int(11) DEFAULT NULL,
  `description` text
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_feedback`
--

CREATE TABLE `tbl_feedback` (
  `id` varchar(40) NOT NULL,
  `reference_no` int(11) NOT NULL,
  `feedback_type` varchar(10) DEFAULT NULL,
  `form_id` varchar(40) NOT NULL,
  `target_id` varchar(40) DEFAULT NULL,
  `date_submitted` datetime DEFAULT NULL,
  `ip_address` varchar(20) DEFAULT NULL,
  `usrname` varchar(255) DEFAULT NULL,
  `status` char(1) NOT NULL DEFAULT 'N'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_feedback_details`
--

CREATE TABLE `tbl_feedback_details` (
  `id` bigint(20) NOT NULL,
  `feedback_id` varchar(40) DEFAULT NULL,
  `param` varchar(250) DEFAULT NULL,
  `param_val` text
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_feedback_files`
--

CREATE TABLE `tbl_feedback_files` (
  `feedback_id` varchar(40) DEFAULT NULL,
  `doc_name` varchar(200) DEFAULT NULL,
  `file_name` varchar(200) DEFAULT NULL,
  `file_path` varchar(200) DEFAULT NULL,
  `file_type` varchar(20) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_feedback_reply`
--

CREATE TABLE `tbl_feedback_reply` (
  `id` bigint(20) NOT NULL,
  `feedback_id` varchar(40) DEFAULT NULL,
  `feedback_from` varchar(255) DEFAULT NULL,
  `feedback_to` varchar(255) DEFAULT NULL,
  `feedback_content` text,
  `date_submitted` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_forms`
--

CREATE TABLE `tbl_forms` (
  `id` varchar(40) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `form_type` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `form_fill_entry_type` char(1) NOT NULL,
  `form_fill_limit` smallint(6) NOT NULL,
  `published` char(1) DEFAULT NULL,
  `show_in_menu` char(1) NOT NULL DEFAULT 'N'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_form_custom_setting`
--

CREATE TABLE `tbl_form_custom_setting` (
  `id` varchar(40) NOT NULL,
  `field_name` varchar(200) DEFAULT NULL,
  `view_label` varchar(200) DEFAULT NULL,
  `view_order` int(11) DEFAULT NULL,
  `class_related` varchar(100) DEFAULT NULL,
  `property_related` varchar(100) DEFAULT NULL,
  `form_id` varchar(40) DEFAULT NULL,
  `return_alias` char(1) DEFAULT NULL,
  `return_eval` text NOT NULL,
  `return_widget` varchar(200) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_form_data`
--

CREATE TABLE `tbl_form_data` (
  `id` bigint(20) NOT NULL,
  `form_submit_id` bigint(20) NOT NULL,
  `param` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `param_val` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_form_files`
--

CREATE TABLE `tbl_form_files` (
  `form_submit_id` int(11) NOT NULL,
  `doc_name` varchar(200) DEFAULT NULL,
  `file_name` varchar(100) NOT NULL,
  `file_path` varchar(200) NOT NULL,
  `file_type` varchar(20) NOT NULL,
  `file_size` int(11) NOT NULL,
  `id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_form_roles`
--

CREATE TABLE `tbl_form_roles` (
  `id` varchar(40) NOT NULL,
  `form_id` varchar(40) DEFAULT NULL,
  `role_id` varchar(40) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_form_submit`
--

CREATE TABLE `tbl_form_submit` (
  `id` bigint(20) NOT NULL,
  `form_id` varchar(40) NOT NULL,
  `host` varchar(40) DEFAULT NULL,
  `usrname` varchar(40) NOT NULL,
  `token` varchar(150) NOT NULL,
  `date_stamp` datetime NOT NULL,
  `ip_address` varchar(30) NOT NULL,
  `url` varchar(200) NOT NULL,
  `published` char(1) NOT NULL,
  `rating` int(11) NOT NULL DEFAULT '0',
  `no_of_views` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_gallery`
--

CREATE TABLE `tbl_gallery` (
  `id` varchar(40) NOT NULL,
  `name` varchar(100) NOT NULL,
  `title` varchar(200) DEFAULT NULL,
  `description` text
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_gallery_image`
--

CREATE TABLE `tbl_gallery_image` (
  `id` varchar(40) NOT NULL,
  `gallery_id` varchar(40) DEFAULT NULL,
  `image_id` varchar(200) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_language`
--

CREATE TABLE `tbl_language` (
  `id` varchar(40) NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` char(10) NOT NULL,
  `description` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_media`
--

CREATE TABLE `tbl_media` (
  `id` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `media_type` int(11) NOT NULL,
  `alt_tag` varchar(100) DEFAULT NULL,
  `caption` varchar(100) DEFAULT NULL,
  `author` int(11) NOT NULL,
  `description` text,
  `upload_date` date NOT NULL,
  `size` int(11) NOT NULL,
  `path` varchar(200) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_media`
--

INSERT INTO `tbl_media` (`id`, `name`, `media_type`, `alt_tag`, `caption`, `author`, `description`, `upload_date`, `size`, `path`) VALUES
('cf98487258357fddf6ceae69cd852a9c6359', 'pexels-photo-261970', 1, 'pexels-photo-261970', NULL, 1, NULL, '2018-09-20', 31011, 'tss4q1cue0m9evebhqg7aqunq7/15374244845ba33c64543681-63116423.jpeg'),
('8c8ddd6d3b66d06c4e4542f089255e563580', 'logo', 1, 'logo', NULL, 1, NULL, '2018-11-22', 93578, 'g3k17g720h21ugp38j1do3pnu6/15428622685bf635bc9b2bd6-88952469.png'),
('310f5ba3feb90599690cc9302d0b263e6830', 'logo', 1, 'logo', NULL, 1, NULL, '2018-11-22', 35460, 'g3k17g720h21ugp38j1do3pnu6/15428903355bf6a35f7b83a7-87479088.png'),
('9fc715d7d3bc8a14d7839723d64f393c6452', 'slide1', 1, 'slide1', NULL, 1, NULL, '2018-11-28', 66927, 'mp6d6l4466n1udeh5ue7ns3nf1/15434463725bff1f64aae875-39042587.jpeg'),
('9fc715d7d3bc8a14d7839723d64f393c3898', 'slide2', 1, 'slide2', NULL, 1, NULL, '2018-11-28', 80867, 'mp6d6l4466n1udeh5ue7ns3nf1/15434463725bff1f64bbc136-05482540.jpeg'),
('9fc715d7d3bc8a14d7839723d64f393c5356', 'slide3', 1, 'slide3', NULL, 1, NULL, '2018-11-28', 75567, 'mp6d6l4466n1udeh5ue7ns3nf1/15434463725bff1f64ca6764-16565506.jpeg'),
('0d306652421d0a8b5666126bdfca85423404', 'pexels-photo-288477', 1, 'pexels-photo-288477', NULL, 1, NULL, '2018-11-28', 21663, 'mp6d6l4466n1udeh5ue7ns3nf1/15434495525bff2bd0d84af7-84600283.jpeg'),
('7f93f664be067f94f6f662222c9f5cf31835', 'outsource', 1, 'outsource', NULL, 1, NULL, '2018-11-29', 23516, 'mp6d6l4466n1udeh5ue7ns3nf1/15434497925bff2cc0a66b38-74510408.jpeg'),
('7f93f664be067f94f6f662222c9f5cf39069', 'pexels-photo-92628', 1, 'pexels-photo-92628', NULL, 1, NULL, '2018-11-29', 15654, 'mp6d6l4466n1udeh5ue7ns3nf1/15434497925bff2cc0b4d2e9-29682857.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_menu`
--

CREATE TABLE `tbl_menu` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_menu`
--

INSERT INTO `tbl_menu` (`id`, `name`, `description`) VALUES
(6, 'Custom', '');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_menu_page`
--

CREATE TABLE `tbl_menu_page` (
  `id` bigint(20) NOT NULL,
  `menu_id` varchar(50) NOT NULL,
  `profile` int(11) NOT NULL,
  `sort_order` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_menu_page`
--

INSERT INTO `tbl_menu_page` (`id`, `menu_id`, `profile`, `sort_order`) VALUES
(19, '53c15ede3bd9b03252dc6f0c5cd33a5c', 3, 100),
(18, 'c95d703647db14eedaea86fc3bb678ec', 3, 90),
(17, '004dd619c771162afaf52c62b55326a2', 3, 80),
(4, 'a5ca6c34-db38-11e3-a2bd-52540079a862', 5, 20),
(16, '4557b44f1734290fd96d5c91a2607a81', 3, 70),
(15, '2c67e6881c74a13c45c2632aa8b6a6ab', 3, 60),
(14, '43213f1036e7c4b8d233a6ff9376f52a', 3, 50),
(13, 'd518d03ffa98b90352caf774510afbb7', 3, 40),
(12, '91ee9106873ce9de8e1597484284d979', 3, 30),
(11, 'd67349a75a65419e623264810e1cae24', 3, 20),
(20, '1cededebf55c6f73069ff7a2e8621eab', 3, 110);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_page`
--

CREATE TABLE `tbl_page` (
  `id` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(255) CHARACTER SET utf8 NOT NULL,
  `menu_title` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `breadcrumb_title` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `url` varchar(50) CHARACTER SET utf8 NOT NULL,
  `description` text CHARACTER SET utf8 NOT NULL,
  `meta_description` text COLLATE utf8_unicode_ci NOT NULL,
  `robots` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT 'INDEX FOLLOW',
  `parent_id` varchar(36) COLLATE utf8_unicode_ci DEFAULT NULL,
  `display_image_id` varchar(36) COLLATE utf8_unicode_ci DEFAULT NULL,
  `layout` varchar(255) CHARACTER SET utf8 NOT NULL,
  `template` varchar(255) CHARACTER SET utf8 NOT NULL,
  `form_id` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `role_id` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `css` int(11) DEFAULT NULL,
  `menu_profile` int(11) DEFAULT NULL,
  `published` tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
  `show_in_menu` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `show_in_footer_menu` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `sidebar` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sort_order` int(3) UNSIGNED NOT NULL DEFAULT '0',
  `permissions` text COLLATE utf8_unicode_ci,
  `master_content` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `sort_order_footer` int(3) DEFAULT '0',
  `editable` int(1) DEFAULT '1',
  `updated` datetime DEFAULT NULL,
  `show_header_image` tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
  `show_footer_image` tinyint(1) DEFAULT NULL,
  `alternate_header_content` text COLLATE utf8_unicode_ci,
  `tab_menu_title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tag_id` varchar(36) COLLATE utf8_unicode_ci DEFAULT '0',
  `hideon_login` char(1) COLLATE utf8_unicode_ci DEFAULT 'N',
  `require_login` char(1) COLLATE utf8_unicode_ci DEFAULT 'N',
  `renderer` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `no_of_views` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `tbl_page`
--

INSERT INTO `tbl_page` (`id`, `title`, `menu_title`, `breadcrumb_title`, `url`, `description`, `meta_description`, `robots`, `parent_id`, `display_image_id`, `layout`, `template`, `form_id`, `role_id`, `css`, `menu_profile`, `published`, `show_in_menu`, `show_in_footer_menu`, `sidebar`, `sort_order`, `permissions`, `master_content`, `sort_order_footer`, `editable`, `updated`, `show_header_image`, `show_footer_image`, `alternate_header_content`, `tab_menu_title`, `tag_id`, `hideon_login`, `require_login`, `renderer`, `no_of_views`) VALUES
('1cededebf55c6f73069ff7a2e8621eab', 'Log Out', 'Log Out', '', 'logout', '<p>Thank you for using our system</p>', '', '', '', '', 'column1', 'b67c34b7887bf859242897740c736222', NULL, NULL, NULL, NULL, 1, 0, 0, NULL, 30, NULL, 1, 0, NULL, '2018-02-23 00:32:36', 1, NULL, NULL, NULL, '', 'N', 'Y', NULL, NULL),
('5e455ec9f9c9d21609d9d5345f2b890f', 'Login', 'Login', '', 'login', '<p>Please log into your account</p>\r\n<p>&nbsp;</p>\r\n<center></center>', '', '', '', '', 'column1', '9f67a476255de2c0afcff03b957ff146', NULL, NULL, NULL, NULL, 1, 0, 0, NULL, 40, NULL, 0, NULL, NULL, '2018-02-22 23:50:01', 1, NULL, NULL, NULL, '', 'Y', 'N', NULL, NULL),
('9d78bca98209b62fb94ae6cf2ea01090', '', 'Home Page', 'Home', 'intro', '<div class="Service section">\r\n<div style="padding: 60px 0 50px 0; background-color: #cdd1d1; width: 100%; height: auto;">\r\n<div class="container">\r\n<div class="row">\r\n<div class="co1-xs-12 col-md-6 col-lg-4">\r\n<div class="servicesContiner ">\r\n<div class="icon float-left">&nbsp;</div>\r\n<div class="ServicesText">\r\n<h4>Training &amp; Development</h4>\r\n<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>\r\n</div>\r\n<div class="clear-fix">&nbsp;</div>\r\n</div>\r\n</div>\r\n<div class="co1-xs-12 col-md-6 col-lg-4">\r\n<div class="servicesContiner ">\r\n<div class="icon float-left">&nbsp;</div>\r\n<div class="ServicesText ">\r\n<h4>Research &amp; Development</h4>\r\n<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>\r\n</div>\r\n<div class="clear-fix">&nbsp;</div>\r\n</div>\r\n</div>\r\n<div class="co1-xs-12 col-md-6 col-lg-4">\r\n<div class="servicesContiner ">\r\n<div class="icon float-left">&nbsp;</div>\r\n<div class="ServicesText ">\r\n<h4>Consultancy</h4>\r\n<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry..</p>\r\n</div>\r\n<div class="clear-fix">&nbsp;</div>\r\n</div>\r\n</div>\r\n</div>\r\n</div>\r\n</div>\r\n</div>', '', '', '', '', 'column1', '9efe43a6a77017df40ba28c6a13368c3', '', '', NULL, NULL, 1, 1, 0, NULL, 20, NULL, 1, 0, NULL, '2018-11-28 23:36:01', 1, NULL, NULL, NULL, '', 'N', 'N', '', 17),
('a5ca6c34-db38-11e3-a2bd-52540079a862', 'Contact', 'Contact Us', 'Contact', 'contact-us', '<p>We would love to hear from you. Feel free to contact us with our contact information below</p>', '', 'INDEX FOLLOW', '', '150405157359a6017525c4d1-46832884', 'column1', '6e5c2c526e55c91f84958c5eba109b6b', '', '', NULL, NULL, 1, 1, 0, NULL, 40, NULL, 1, 0, NULL, '2018-11-28 22:45:35', 1, NULL, NULL, NULL, '', 'N', 'N', '', 2),
('bd22a42ed957ffd7a171501aa7886b48', 'Sign Up', 'Sign Up', 'Sign Up', 'signup', '', '', '', '', '', 'column1', 'de55671b11eac389d54450ae24b71653', '', '', NULL, NULL, 1, 0, 0, NULL, 50, NULL, 1, 0, NULL, '2018-11-19 23:13:11', 1, NULL, NULL, NULL, '', 'Y', 'N', '', NULL),
('d5f3d315b4ba211f11c5a06752898d1b', 'Blog Directory', 'Blog Listing', 'Blog Listing', 'blog-directory', '', '', '', '', '', 'column1', '0323a8564428391de276193808b48f45', '', '', NULL, NULL, 1, 0, 0, NULL, 20, NULL, 1, 0, NULL, '2018-11-22 22:57:08', 1, NULL, NULL, NULL, '', 'N', 'N', '', NULL),
('dc5f6b778e4d259ba74892c76acc63dd', 'Forgot Password', 'Forgot Password', 'Forgot Password', 'forgot-password', '<p>If you have forgotten your password, fill in the form below to change your password</p>', '', '', '', '', 'column1', '63aaa01fb0126e490547923279cbcf3f', '', '', NULL, NULL, 1, 0, 0, NULL, 70, NULL, 1, 0, NULL, '2018-04-16 14:10:16', 1, NULL, NULL, NULL, '', 'Y', 'N', NULL, NULL),
('f0769958b0fba394ae53a0d261b445b9', 'Successful Registration', 'Success', '', 'registration-success', '<p>Thank you for registering to the church website. Please log into your account to post articles of testimonies and articles.</p>', '', '', '', '', 'column1', 'e5a63fe92ede5dceadd0e88930567573', NULL, NULL, NULL, NULL, 1, 0, 0, NULL, 80, NULL, 1, 0, NULL, '2018-02-20 20:38:05', 1, NULL, NULL, NULL, 'Yii::$app->request->post("tag_id")', 'N', 'N', NULL, NULL),
('fe43812374f05721712180a04eea8e42', 'Sample Blog Index', 'Blog', 'Blog', 'blogs', '', '', '', '', '', 'column1', 'a7c49fad2de03424add30b0a31451377', '', '', NULL, NULL, 1, 1, 1, NULL, 30, NULL, 1, 0, NULL, '2018-11-22 23:11:37', 1, NULL, NULL, NULL, '', 'N', 'N', '', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_page_tags`
--

CREATE TABLE `tbl_page_tags` (
  `page_id` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `tags_id` varchar(36) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_page_tag_index`
--

CREATE TABLE `tbl_page_tag_index` (
  `index_tag_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `page_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_profile_details`
--

CREATE TABLE `tbl_profile_details` (
  `id` int(11) NOT NULL,
  `profile_id` int(11) DEFAULT NULL,
  `param` varchar(250) DEFAULT NULL,
  `param_val` text
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_rating_details`
--

CREATE TABLE `tbl_rating_details` (
  `id` varchar(40) NOT NULL,
  `rating_id` int(11) NOT NULL,
  `target_type` char(2) NOT NULL,
  `target_id` varchar(40) NOT NULL,
  `rated_by` varchar(250) NOT NULL,
  `rate_date` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_rating_profile`
--

CREATE TABLE `tbl_rating_profile` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `title` varchar(100) NOT NULL,
  `default_label` varchar(100) NOT NULL,
  `description` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_rating_profile_details`
--

CREATE TABLE `tbl_rating_profile_details` (
  `id` int(11) NOT NULL,
  `profile_id` int(11) NOT NULL,
  `rating_name` varchar(40) NOT NULL,
  `rating_value` int(11) NOT NULL,
  `rating_rgb_color` char(8) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_relationships`
--

CREATE TABLE `tbl_relationships` (
  `id` varchar(40) NOT NULL,
  `title` varchar(200) DEFAULT NULL,
  `name` varchar(200) DEFAULT NULL,
  `source_type` varchar(20) DEFAULT NULL,
  `source_id` varchar(40) DEFAULT NULL,
  `target_type` varchar(20) DEFAULT NULL,
  `target_id` varchar(40) DEFAULT NULL,
  `notes` text
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_relationship_details`
--

CREATE TABLE `tbl_relationship_details` (
  `id` varchar(40) NOT NULL,
  `relationship_id` varchar(40) DEFAULT NULL,
  `source_field` varchar(200) DEFAULT NULL,
  `target_field` varchar(200) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_reports`
--

CREATE TABLE `tbl_reports` (
  `id` varchar(40) NOT NULL,
  `name` varchar(100) NOT NULL,
  `alias` varchar(200) NOT NULL,
  `description` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_resources`
--

CREATE TABLE `tbl_resources` (
  `id` varchar(40) NOT NULL,
  `alias` varchar(100) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `resource_type` char(10) DEFAULT NULL,
  `resource` varchar(40) DEFAULT NULL,
  `description` text
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_roles`
--

CREATE TABLE `tbl_roles` (
  `id` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `access_type` char(1) NOT NULL DEFAULT 'F',
  `menu_id` tinyint(4) NOT NULL,
  `parent_role_id` varchar(30) DEFAULT NULL,
  `permissions` text
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_roles`
--

INSERT INTO `tbl_roles` (`id`, `name`, `description`, `access_type`, `menu_id`, `parent_role_id`, `permissions`) VALUES
('996fcbfa34a4a905f49670b9689579', 'Users', '', 'F', 3, 'f2d16e5cedb1c39a8630b775974565', NULL),
('f2d16e5cedb1c39a8630b775974565', 'Administrator', 'This is the administrators side', 'B', 0, '', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_services_incoming`
--

CREATE TABLE `tbl_services_incoming` (
  `id` varchar(40) NOT NULL,
  `name` varchar(200) NOT NULL,
  `client_id` varchar(40) NOT NULL,
  `client_key` varchar(40) NOT NULL,
  `ip_address` varchar(200) NOT NULL,
  `rate_limit` int(11) NOT NULL,
  `authentication_token` varchar(40) DEFAULT NULL,
  `resources` text
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_services_outgoing`
--

CREATE TABLE `tbl_services_outgoing` (
  `id` varchar(40) NOT NULL,
  `name` varchar(200) NOT NULL,
  `client_id` varchar(40) NOT NULL,
  `client_key` varchar(200) NOT NULL,
  `header` text NOT NULL,
  `config` text
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_settings`
--

CREATE TABLE `tbl_settings` (
  `id` int(11) NOT NULL,
  `setting_name` varchar(50) NOT NULL,
  `setting_value` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_settings`
--

INSERT INTO `tbl_settings` (`id`, `setting_name`, `setting_value`) VALUES
(1, 'current_theme', '10'),
(2, 'website_name', 'My Website Name'),
(3, 'website_short_name', ''),
(4, 'website_logo', '310f5ba3feb90599690cc9302d0b263e6830'),
(5, 'website_image_url', 'http://localhost/yumpeecms/uploads'),
(6, 'website_home_page', '9d78bca98209b62fb94ae6cf2ea01090'),
(7, 'contact_us_email', 'myemail@myserver.com'),
(8, 'google_map_key', ''),
(9, 'contact_us_address', '44 Price Street\r\nNambour<br>\r\n\r\nTelephone : 04123312234<br>\r\n\r\nEmail :tinywebsite@mysite.com<br>\r\n'),
(10, 'date_format', 'F j, Y'),
(11, 'breadcrumbs', 'off'),
(12, 'page_size', '5'),
(13, 'time_format', 'h:m A'),
(14, 'smtp_host', 'smtp.gmail.com'),
(15, 'smtp_port', '456'),
(16, 'smtp_connection', 'ssl'),
(17, 'smtp_username', 'yourname@gmail.com'),
(18, 'smtp_password', 'password'),
(19, 'smtp_sender_email', 'peter@gmail.com'),
(20, 'smtp_sender_name', 'Peter Odon'),
(21, 'smtp_use_smtp', 'No'),
(24, 'auto_approve_comments', 'on'),
(25, 'current_theme_header', 'No'),
(26, 'current_theme_footer', 'No'),
(27, 'container_display_type', 'fluid'),
(28, 'error_page', '0'),
(29, 'home_url', 'http://localhost/yumpeecms/frontend/web'),
(30, 'twig_template', 'Yes'),
(31, 'registration_role', '996fcbfa34a4a905f49670b9689579'),
(32, 'registration_page', 'f0769958b0fba394ae53a0d261b445b9'),
(33, 'auto_approve_post', 'on'),
(34, 'fav_icon', 'dfd092025833a32cb7f7cd12fa11addb6963'),
(35, 'maintenance_mode', 'No'),
(36, 'maintenance_page', 'a5ca6c34-db38-11e3-a2bd-52540079a862'),
(37, 'captcha', 'off'),
(38, 'captcha_public', '6Ldk4hETAAAAAOXztn80aeTzCS6VbZ2T_dmAIwKK'),
(39, 'captcha_private', '7Ldk4hETAAAAAOXztn80aeTzCS6VbZ2T_dmAIwKK'),
(40, 'minify_javascript', 'off'),
(41, 'minify_css', 'off'),
(42, 'minify_twig', 'off'),
(43, 'use_custom_backend_menus', 'on'),
(44, 'seo_meta_tags', '<meta name="description" content="YumpeeCMS - An application development framework"/>\r\n'),
(45, 'backend_home_page', ''),
(46, 'allow_multiple_domains', 'No');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_sliders`
--

CREATE TABLE `tbl_sliders` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `title` varchar(200) DEFAULT NULL,
  `description` text,
  `transition_type` char(1) NOT NULL,
  `duration` int(11) NOT NULL,
  `default_height` int(11) NOT NULL,
  `default_width` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_slider_image`
--

CREATE TABLE `tbl_slider_image` (
  `id` int(11) NOT NULL,
  `slider_id` int(11) NOT NULL,
  `media_id` varchar(200) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_subscriptions`
--

CREATE TABLE `tbl_subscriptions` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `category_id` varchar(40) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_subscription_categories`
--

CREATE TABLE `tbl_subscription_categories` (
  `id` varchar(40) NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  `description` text
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_tags`
--

CREATE TABLE `tbl_tags` (
  `id` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `url` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `master_content` tinyint(4) NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `display_image_id` varchar(80) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_tags_index`
--

CREATE TABLE `tbl_tags_index` (
  `index_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `tags_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_tag_types`
--

CREATE TABLE `tbl_tag_types` (
  `id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_templates`
--

CREATE TABLE `tbl_templates` (
  `id` varchar(32) CHARACTER SET utf8 NOT NULL,
  `name` varchar(32) CHARACTER SET utf8 NOT NULL,
  `route` varchar(100) CHARACTER SET utf8 NOT NULL,
  `url` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `internal_route_stat` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `parent_id` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `renderer` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `tbl_templates`
--

INSERT INTO `tbl_templates` (`id`, `name`, `route`, `url`, `internal_route_stat`, `parent_id`, `renderer`) VALUES
('83ebb876a836e3e9ca2ecdf74b236809', 'Blog Details', 'blog/details', '', 'N', NULL, NULL),
('6e5c2c526e55c91f84958c5eba109b6b', 'Contact', 'contact/index', '', 'N', NULL, NULL),
('8255ac5bb21df6eeea6af3fee05182d8', 'Our Team', 'standard/team', '', 'N', NULL, NULL),
('e5a63fe92ede5dceadd0e88930567573', 'Standard Web Page', 'standard/index', '', 'N', NULL, NULL),
('0323a8564428391de276193808b48f45', 'Blog Category Index', 'blog/category', '', 'N', NULL, NULL),
('109eeb57032a3a3059386eb68f302f4d', 'Testimonials', 'testimonial/index', '', 'N', NULL, NULL),
('de55671b11eac389d54450ae24b71653', 'Registration', 'registration/index', '', 'N', NULL, NULL),
('f7791e3bfaabbeeb811f7df63eb3182a', 'Form Search', 'forms/search', NULL, 'N', NULL, NULL),
('63aaa01fb0126e490547923279cbcf3f', 'Forgot Password', 'accounts/password', NULL, 'N', NULL, NULL),
('9f67a476255de2c0afcff03b957ff146', 'Login', 'accounts/login', '', 'N', NULL, NULL),
('abf6b50d5ef16f173c061ab281dab658', 'User Listing', 'roles/index', '', 'N', NULL, NULL),
('5a53cbd9f2b9ab21765dd0ba93c70692', 'Form View', 'forms/view', NULL, 'N', NULL, NULL),
('cb93adea31402b8a32cf78fb61bed701', 'Rating', 'tags/rating', 'rating', 'Y', NULL, NULL),
('e4319cda3f8d748860142c2eae5a92a6', 'Form Page', 'forms/display', NULL, 'N', NULL, NULL),
('b67c34b7887bf859242897740c736222', 'Logout', 'accounts/logout', NULL, 'N', NULL, NULL),
('e429ededec4b0a49a7c6fdb6f2355fa8', 'Search Articles', 'tags/search', 'search', 'Y', NULL, NULL),
('64f2c0ffcdbb14c32bb7272cc9539c8d', 'Archives Directory', 'tags/archives', 'archives', 'Y', NULL, NULL),
('a7c49fad2de03424add30b0a31451377', 'Blog Article Index', 'blog/index', '', 'N', NULL, NULL),
('8b6df117cde4989a760650bc63adec3b', 'Tags Index', 'tags/index', '', 'Y', NULL, NULL),
('9edebf242f4d37051c1244bb02bce4d6', 'Authors Directory', 'tags/authors', 'authors', 'Y', NULL, NULL),
('ba2fd87c31801f2196a71a0db2c4da17', 'User Details', 'roles/details', NULL, 'N', NULL, NULL),
('9efe43a6a77017df40ba28c6a13368c3', 'Home Page', 'standard/standard/home-page', '', 'N', 'e5a63fe92ede5dceadd0e88930567573', 'standard/standard/home-page');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_template_widget`
--

CREATE TABLE `tbl_template_widget` (
  `id` bigint(20) NOT NULL,
  `page_id` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `widget` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `display_order` int(11) NOT NULL,
  `position` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `settings` text COLLATE utf8_unicode_ci
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `tbl_template_widget`
--

INSERT INTO `tbl_template_widget` (`id`, `page_id`, `widget`, `display_order`, `position`, `settings`) VALUES
(40, '0323a8564428391de276193808b48f45', 'widget_articles', 1, 'side', '{"widget_limit":"2","widget_title":"My Articles","widget_name":"widget_articles","widget_page_id":"0323a8564428391de276193808b48f45","id":"40"}'),
(39, '0323a8564428391de276193808b48f45', 'widget_contractors', 1, 'side', ''),
(127, 'e5a63fe92ede5dceadd0e88930567573', 'widget_recent_article', 5, 'side', ''),
(139, 'a7c49fad2de03424add30b0a31451377', 'widget_articles', 2, 'side', '{"widget_limit":"2","widget_title":"My Articles","widget_name":"widget_articles","widget_page_id":"a7c49fad2de03424add30b0a31451377","id":"139"}'),
(140, 'a7c49fad2de03424add30b0a31451377', 'widget_archives', 1, 'side', '{"widget_limit":"5","widget_title":"Archives","widget_name":"widget_archives","widget_page_id":"a7c49fad2de03424add30b0a31451377","id":"140"}'),
(141, 'a7c49fad2de03424add30b0a31451377', 'widget_html', 2, 'side', ''),
(125, 'e5a63fe92ede5dceadd0e88930567573', 'widget_category', 3, 'side', ''),
(69, '6e5c2c526e55c91f84958c5eba109b6b', 'widget_html', 6, 'side', ''),
(68, '6e5c2c526e55c91f84958c5eba109b6b', 'widget_feature_page', 2, 'side', '{"widget_limit":"2","widget_title":"Sample Article","widget_name":"widget_feature_page","widget_page_id":"6e5c2c526e55c91f84958c5eba109b6b","id":"68"}'),
(38, '0323a8564428391de276193808b48f45', 'widget_employers', 3, 'side', ''),
(67, '6e5c2c526e55c91f84958c5eba109b6b', 'widget_testimonials', 2, 'side', '{"widget_limit":"","widget_title":"Testimonials","widget_name":"widget_testimonials","widget_page_id":"6e5c2c526e55c91f84958c5eba109b6b","id":"67"}'),
(229, '9edebf242f4d37051c1244bb02bce4d6', 'widget_articles', 2, 'side', '{"widget_limit":"2","widget_title":"My Articles","widget_name":"widget_articles","widget_page_id":"9edebf242f4d37051c1244bb02bce4d6","id":"229"}'),
(135, 'e5a63fe92ede5dceadd0e88930567573', 'widget_menu', 7, 'side', ''),
(134, 'e5a63fe92ede5dceadd0e88930567573', 'widget_html', 6, 'side', ''),
(133, 'e5a63fe92ede5dceadd0e88930567573', 'widget_feature_page', 4, 'side', ''),
(132, 'e5a63fe92ede5dceadd0e88930567573', 'widget_testimonials', 9, 'side', ''),
(165, '8b6df117cde4989a760650bc63adec3b', 'widget_archives', 1, 'side', ''),
(164, '8b6df117cde4989a760650bc63adec3b', 'widget_articles', 2, 'side', ''),
(162, '8b6df117cde4989a760650bc63adec3b', 'widget_category', 3, 'side', ''),
(163, '8b6df117cde4989a760650bc63adec3b', 'widget_tag_cloud', 4, 'side', ''),
(231, '9edebf242f4d37051c1244bb02bce4d6', 'widget_blog_article', 3, 'bottom', '{"widget_limit":"1","widget_title":"Blogs","widget_name":"widget_blog_article","widget_page_id":"9edebf242f4d37051c1244bb02bce4d6","id":"231"}'),
(268, '9efe43a6a77017df40ba28c6a13368c3', 'widget_blog_article', 1, 'yumpee_pos_blog_articles', '{"widget_limit":"3","widget_title":"Latest Blog","widget_name":"widget_blog_article","widget_page_id":"9efe43a6a77017df40ba28c6a13368c3","id":"268"}'),
(41, '0323a8564428391de276193808b48f45', 'widget_archives', 2, 'side', ''),
(130, 'e5a63fe92ede5dceadd0e88930567573', 'widget_subscription', 7, 'side', ''),
(131, 'e5a63fe92ede5dceadd0e88930567573', 'widget_archives', 1, 'side', ''),
(129, 'e5a63fe92ede5dceadd0e88930567573', 'widget_search', 10, 'side', ''),
(128, 'e5a63fe92ede5dceadd0e88930567573', 'widget_social', 6, 'bottom', ''),
(230, '9edebf242f4d37051c1244bb02bce4d6', 'widget_archives', 1, 'side', '{"widget_limit":"1","widget_title":"TEst Archives","widget_name":"widget_archives","widget_page_id":"9edebf242f4d37051c1244bb02bce4d6","id":"230"}'),
(126, 'e5a63fe92ede5dceadd0e88930567573', 'widget_tag_cloud', 8, 'side', ''),
(224, '83ebb876a836e3e9ca2ecdf74b236809', 'widget_articles', 2, 'side', '{"widget_limit":"3","widget_title":"Other Stuff to Read","widget_name":"widget_articles","widget_page_id":"83ebb876a836e3e9ca2ecdf74b236809","id":"224"}'),
(220, '83ebb876a836e3e9ca2ecdf74b236809', 'widget_category', 3, 'side', '{"widget_limit":"3","widget_title":"Our Category","widget_name":"widget_category","widget_page_id":"83ebb876a836e3e9ca2ecdf74b236809","id":"220"}'),
(264, 'a7c49fad2de03424add30b0a31451377', 'widget_category', 5, 'side', '{"widget_limit":"5","widget_title":"Categories","widget_name":"widget_category","widget_page_id":"a7c49fad2de03424add30b0a31451377","id":"264"}'),
(239, 'abf6b50d5ef16f173c061ab281dab658', 'widget_audio', 2, 'side', ''),
(267, '9efe43a6a77017df40ba28c6a13368c3', 'widget_html', 1, 'yumpee_pos_home-page-slider', '{"widget_limit":"d6dcf609cea16be319bc3197bc0eaef4","widget_title":"","widget_name":"widget_html","widget_page_id":"9efe43a6a77017df40ba28c6a13368c3","id":"267"}'),
(242, 'a7c49fad2de03424add30b0a31451377', 'widget_articles', 2, 'side', ''),
(243, 'a7c49fad2de03424add30b0a31451377', 'widget_archives', 1, 'side', ''),
(244, 'a7c49fad2de03424add30b0a31451377', 'widget_html', 2, 'side', ''),
(245, 'a7c49fad2de03424add30b0a31451377', 'widget_articles', 2, 'side', ''),
(246, 'a7c49fad2de03424add30b0a31451377', 'widget_archives', 1, 'side', ''),
(247, 'a7c49fad2de03424add30b0a31451377', 'widget_html', 2, 'side', ''),
(248, 'a7c49fad2de03424add30b0a31451377', 'widget_articles', 2, 'side', ''),
(249, 'a7c49fad2de03424add30b0a31451377', 'widget_archives', 1, 'side', ''),
(250, 'a7c49fad2de03424add30b0a31451377', 'widget_html', 2, 'side', ''),
(251, 'a7c49fad2de03424add30b0a31451377', 'widget_articles', 2, 'side', ''),
(252, 'a7c49fad2de03424add30b0a31451377', 'widget_archives', 1, 'side', ''),
(253, 'a7c49fad2de03424add30b0a31451377', 'widget_html', 2, 'side', ''),
(254, 'a7c49fad2de03424add30b0a31451377', 'widget_articles', 2, 'side', ''),
(255, 'a7c49fad2de03424add30b0a31451377', 'widget_archives', 1, 'side', ''),
(256, 'a7c49fad2de03424add30b0a31451377', 'widget_html', 2, 'side', ''),
(257, 'a7c49fad2de03424add30b0a31451377', 'widget_audio', 3, 'side', ''),
(259, '67397c01e4912722824d93df8d107c64', 'widget_blog_article', 1, 'side', ''),
(260, 'e6a5f5a177c08dd8c6335c7293372bea', 'widget_articles', 1, 'side', ''),
(261, 'e6a5f5a177c08dd8c6335c7293372bea', 'widget_blog_article', 2, 'side', ''),
(262, 'f3610c044653d7a63274cc3a3966b895', 'widget_articles', 2, 'side', '{"widget_limit":"3","widget_title":"News Articles","widget_name":"widget_articles","widget_page_id":"f3610c044653d7a63274cc3a3966b895","id":"262"}'),
(269, '83ebb876a836e3e9ca2ecdf74b236809', 'widget_comment', 4, 'bottom', '{"widget_limit":"10","widget_title":"Comments","widget_name":"widget_comment","widget_page_id":"83ebb876a836e3e9ca2ecdf74b236809","id":"269"}');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_testimonials`
--

CREATE TABLE `tbl_testimonials` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `company` varchar(200) DEFAULT NULL,
  `author` varchar(100) NOT NULL,
  `author_position` varchar(100) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_themes`
--

CREATE TABLE `tbl_themes` (
  `id` int(11) NOT NULL,
  `folder` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `stylesheet` text,
  `javascript` text,
  `is_default` tinyint(1) NOT NULL,
  `description` text,
  `header` text,
  `footer` text,
  `custom_styles` text,
  `settings_file` text
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_themes`
--

INSERT INTO `tbl_themes` (`id`, `folder`, `name`, `stylesheet`, `javascript`, `is_default`, `description`, `header`, `footer`, `custom_styles`, `settings_file`) VALUES
(10, 'default2018', 'Yumpe Default Blog', 'css/bootstrap.min.css;css/owl.carousel.min.css;css/owl.theme.default.css;editor/summernote-bs4.css;css/style.css;css/slicknav.css', 'js/popper.min.js;js/bootstrap.min.js;editor/summernote.bs4.js;js/owl.carousel.min.js;js/owl.carousel.js;js/wow.js;js/jquery.slicknav.js;js/main.js', 1, NULL, '', '', '', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_translation_category`
--

CREATE TABLE `tbl_translation_category` (
  `id` varchar(40) NOT NULL,
  `name` varchar(40) DEFAULT NULL,
  `alias` varchar(100) DEFAULT NULL,
  `description` text
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_twig`
--

CREATE TABLE `tbl_twig` (
  `id` int(11) NOT NULL,
  `theme_id` int(11) NOT NULL,
  `renderer` varchar(50) NOT NULL,
  `renderer_type` char(1) NOT NULL,
  `code` text NOT NULL,
  `filename` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_twig`
--

INSERT INTO `tbl_twig` (`id`, `theme_id`, `renderer`, `renderer_type`, `code`, `filename`) VALUES
(35, 10, 'standard/standard/home-page', 'V', '<div id="yumpee_pos_home-page-slider"></div>\r\n\r\n<div id="yumpee_pos_blog_articles">\r\n                \r\n</div>', 'b321543cc48ad3cead42af2de51a733c.twig');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user`
--

CREATE TABLE `tbl_user` (
  `id` int(11) NOT NULL,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `first_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `last_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `title` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `role_id` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `auth_key` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password_hash` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password_reset_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `extension` int(11) DEFAULT NULL,
  `status` smallint(6) NOT NULL DEFAULT '10',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `display_image_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `about` text COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `tbl_user`
--

INSERT INTO `tbl_user` (`id`, `username`, `first_name`, `last_name`, `title`, `role_id`, `auth_key`, `password_hash`, `password_reset_token`, `email`, `extension`, `status`, `created_at`, `updated_at`, `display_image_id`, `about`) VALUES
(1, 'admin', 'Peter', 'Odon', 'Administrator', 'f2d16e5cedb1c39a8630b775974565', '', '$2y$13$krxMlDgSnaEqfzrS51mRd.4diQioXySM3NeG5PiSO3kyZUcxHZDvi', NULL, 'info@yumpeecms.com', NULL, 10, 0, 1532655302, 'eb4f8864d080684d555b39c3194f1afd2565', 'Peter is a web developer');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user_profile_files`
--

CREATE TABLE `tbl_user_profile_files` (
  `profile_id` int(11) NOT NULL,
  `doc_name` varchar(200) DEFAULT NULL,
  `file_name` varchar(100) NOT NULL,
  `file_path` varchar(200) NOT NULL,
  `file_type` varchar(20) NOT NULL,
  `file_size` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_web_hook`
--

CREATE TABLE `tbl_web_hook` (
  `id` int(11) NOT NULL,
  `end_point` text NOT NULL,
  `json_data` text NOT NULL,
  `form_id` varchar(40) NOT NULL,
  `client_profile` varchar(40) DEFAULT NULL,
  `hook_type` char(1) NOT NULL,
  `post_type` char(1) DEFAULT NULL,
  `response_target` varchar(40) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_web_hook_email`
--

CREATE TABLE `tbl_web_hook_email` (
  `id` int(11) NOT NULL,
  `form_id` varchar(40) NOT NULL,
  `email` varchar(250) NOT NULL,
  `webhook_type` char(1) NOT NULL,
  `subject` varchar(250) NOT NULL,
  `message` text NOT NULL,
  `include_data` char(1) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_widgets`
--

CREATE TABLE `tbl_widgets` (
  `id` int(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `short_name` varchar(50) NOT NULL,
  `setting_value` text NOT NULL,
  `template_type` char(1) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `permissions` text,
  `require_login` char(1) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_widgets`
--

INSERT INTO `tbl_widgets` (`id`, `name`, `short_name`, `setting_value`, `template_type`, `parent_id`, `permissions`, `require_login`) VALUES
(1, 'Categories', 'widget_category', '', 'S', 0, NULL, NULL),
(2, 'Tag Cloud', 'widget_tag_cloud', '', 'S', 0, NULL, NULL),
(3, 'Most Recent Article', 'widget_recent_article', '', 'S', 0, NULL, NULL),
(6, 'Social Media', 'widget_social', '', 'S', 0, NULL, NULL),
(7, 'Search Website', 'widget_search', '', 'S', 0, NULL, NULL),
(8, 'Subscription', 'widget_subscription', '', 'S', 0, NULL, NULL),
(9, 'Contact Form', 'widget_contact', '', 'S', 0, NULL, NULL),
(10, 'Articles', 'widget_articles', '', 'S', 0, NULL, NULL),
(11, 'Comment', 'widget_comment', '', 'S', 0, NULL, NULL),
(12, 'Google Maps', 'widget_google_maps', '', 'S', 0, NULL, NULL),
(13, 'Social Count', 'widget_social_count', '', 'S', 0, NULL, NULL),
(14, 'Image Widget', 'widget_image', '', 'S', 0, NULL, NULL),
(15, 'Archives', 'widget_archives', '', 'S', 0, NULL, NULL),
(16, 'Youtube Channel Gallery', 'widget_youtube', '', 'S', NULL, NULL, NULL),
(17, 'Login', 'widget_login', '', 'S', 0, NULL, NULL),
(18, 'Testimonials', 'widget_testimonials', '', 'S', 0, NULL, NULL),
(19, 'Random Posts', 'widget_random_post', '', 'S', 0, NULL, NULL),
(20, 'Feature Page', 'widget_feature_page', '', 'S', 0, NULL, NULL),
(21, 'Slider Widget', 'widget_slider', '', 'S', 0, NULL, NULL),
(23, 'HTML', 'widget_html', '', 'S', 0, NULL, NULL),
(24, 'Menu', 'widget_menu', '', 'S', 0, NULL, NULL),
(25, 'Blog Index Article', 'widget_blog_article', '', 'S', 0, NULL, NULL),
(27, 'Rating', 'widget_rating', '', 'S', 0, NULL, NULL),
(30, 'Video', 'widget_video', '', 'S', NULL, NULL, NULL),
(31, 'Audio', 'widget_audio', '', 'S', NULL, NULL, NULL),
(32, 'ReCAPTCHA', 'widget_recaptcha', '', 'S', NULL, NULL, NULL),
(33, 'Gallery', 'widget_gallery', '', 'S', 0, NULL, NULL),
(36, 'Breadcrumbs', 'widget_breadcrumbs', '', 'S', 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_widget_position`
--

CREATE TABLE `tbl_widget_position` (
  `id` varchar(40) NOT NULL,
  `title` varchar(50) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `description` text
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_widget_position`
--

INSERT INTO `tbl_widget_position` (`id`, `title`, `name`, `description`) VALUES
('56', 'Hero Position', 'yumpee_pos_hero-position', ''),
('ew3444', 'Page Slider', 'yumpee_pos_home-page-slider', ''),
('6', 'Home Page Blog', 'yumpee_pos_blog_articles', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`id`,`language`);

--
-- Indexes for table `sourcemessage`
--
ALTER TABLE `sourcemessage`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_access_tokens`
--
ALTER TABLE `tbl_access_tokens`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_articles`
--
ALTER TABLE `tbl_articles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `url` (`url`),
  ADD KEY `display_image_id` (`display_image_id`),
  ADD KEY `usrname` (`usrname`);

--
-- Indexes for table `tbl_articles_blog_index`
--
ALTER TABLE `tbl_articles_blog_index`
  ADD KEY `articles_id` (`articles_id`),
  ADD KEY `blog_index_id` (`blog_index_id`);

--
-- Indexes for table `tbl_articles_category`
--
ALTER TABLE `tbl_articles_category`
  ADD PRIMARY KEY (`id`),
  ADD KEY `display_image_id` (`display_image_id`);

--
-- Indexes for table `tbl_articles_category_index`
--
ALTER TABLE `tbl_articles_category_index`
  ADD KEY `category_id` (`category_id`),
  ADD KEY `category_index_id` (`category_index_id`);

--
-- Indexes for table `tbl_articles_category_related`
--
ALTER TABLE `tbl_articles_category_related`
  ADD KEY `articles_id` (`articles_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `tbl_articles_tag`
--
ALTER TABLE `tbl_articles_tag`
  ADD KEY `articles_id` (`articles_id`),
  ADD KEY `tags_id` (`tags_id`);

--
-- Indexes for table `tbl_article_details`
--
ALTER TABLE `tbl_article_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_article_media`
--
ALTER TABLE `tbl_article_media`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_authorization_codes`
--
ALTER TABLE `tbl_authorization_codes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_backend_menu`
--
ALTER TABLE `tbl_backend_menu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_backend_menu_role`
--
ALTER TABLE `tbl_backend_menu_role`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_block`
--
ALTER TABLE `tbl_block`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_block_group`
--
ALTER TABLE `tbl_block_group`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_block_group_list`
--
ALTER TABLE `tbl_block_group_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_block_page`
--
ALTER TABLE `tbl_block_page`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `block_id_page_id` (`page_id`,`block_id`),
  ADD KEY `block_id` (`block_id`),
  ADD KEY `page_id` (`page_id`);

--
-- Indexes for table `tbl_categories`
--
ALTER TABLE `tbl_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_class_attributes`
--
ALTER TABLE `tbl_class_attributes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_class_elements`
--
ALTER TABLE `tbl_class_elements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_class_elements_attributes`
--
ALTER TABLE `tbl_class_elements_attributes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_class_setup`
--
ALTER TABLE `tbl_class_setup`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_client_resources`
--
ALTER TABLE `tbl_client_resources`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_comments`
--
ALTER TABLE `tbl_comments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_css`
--
ALTER TABLE `tbl_css`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_custom_settings`
--
ALTER TABLE `tbl_custom_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_custom_widget`
--
ALTER TABLE `tbl_custom_widget`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_domains`
--
ALTER TABLE `tbl_domains`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_feedback`
--
ALTER TABLE `tbl_feedback`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQUE` (`reference_no`);

--
-- Indexes for table `tbl_feedback_details`
--
ALTER TABLE `tbl_feedback_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_feedback_files`
--
ALTER TABLE `tbl_feedback_files`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_feedback_reply`
--
ALTER TABLE `tbl_feedback_reply`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_forms`
--
ALTER TABLE `tbl_forms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_form_custom_setting`
--
ALTER TABLE `tbl_form_custom_setting`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_form_data`
--
ALTER TABLE `tbl_form_data`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_form_files`
--
ALTER TABLE `tbl_form_files`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_form_roles`
--
ALTER TABLE `tbl_form_roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_form_submit`
--
ALTER TABLE `tbl_form_submit`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_gallery`
--
ALTER TABLE `tbl_gallery`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_gallery_image`
--
ALTER TABLE `tbl_gallery_image`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_language`
--
ALTER TABLE `tbl_language`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_media`
--
ALTER TABLE `tbl_media`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_menu`
--
ALTER TABLE `tbl_menu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_menu_page`
--
ALTER TABLE `tbl_menu_page`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_page`
--
ALTER TABLE `tbl_page`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `url` (`url`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `display_image_id` (`display_image_id`),
  ADD KEY `template` (`template`);

--
-- Indexes for table `tbl_page_tags`
--
ALTER TABLE `tbl_page_tags`
  ADD KEY `page_id` (`page_id`),
  ADD KEY `tags_id` (`tags_id`);

--
-- Indexes for table `tbl_page_tag_index`
--
ALTER TABLE `tbl_page_tag_index`
  ADD KEY `index_tag_id` (`index_tag_id`),
  ADD KEY `page_id` (`page_id`);

--
-- Indexes for table `tbl_profile_details`
--
ALTER TABLE `tbl_profile_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_rating_details`
--
ALTER TABLE `tbl_rating_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_rating_profile`
--
ALTER TABLE `tbl_rating_profile`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_rating_profile_details`
--
ALTER TABLE `tbl_rating_profile_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_relationships`
--
ALTER TABLE `tbl_relationships`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_relationship_details`
--
ALTER TABLE `tbl_relationship_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_reports`
--
ALTER TABLE `tbl_reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_resources`
--
ALTER TABLE `tbl_resources`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_roles`
--
ALTER TABLE `tbl_roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_services_incoming`
--
ALTER TABLE `tbl_services_incoming`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_services_outgoing`
--
ALTER TABLE `tbl_services_outgoing`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_settings`
--
ALTER TABLE `tbl_settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `setting_name` (`setting_name`);

--
-- Indexes for table `tbl_sliders`
--
ALTER TABLE `tbl_sliders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_slider_image`
--
ALTER TABLE `tbl_slider_image`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_subscriptions`
--
ALTER TABLE `tbl_subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `tbl_subscription_categories`
--
ALTER TABLE `tbl_subscription_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_tags`
--
ALTER TABLE `tbl_tags`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_tag_types`
--
ALTER TABLE `tbl_tag_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_templates`
--
ALTER TABLE `tbl_templates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `url` (`url`);

--
-- Indexes for table `tbl_template_widget`
--
ALTER TABLE `tbl_template_widget`
  ADD PRIMARY KEY (`id`),
  ADD KEY `page_id` (`page_id`),
  ADD KEY `widget` (`widget`);

--
-- Indexes for table `tbl_testimonials`
--
ALTER TABLE `tbl_testimonials`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_themes`
--
ALTER TABLE `tbl_themes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_translation_category`
--
ALTER TABLE `tbl_translation_category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_twig`
--
ALTER TABLE `tbl_twig`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_user`
--
ALTER TABLE `tbl_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `password_reset_token` (`password_reset_token`);

--
-- Indexes for table `tbl_web_hook`
--
ALTER TABLE `tbl_web_hook`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_web_hook_email`
--
ALTER TABLE `tbl_web_hook_email`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_widgets`
--
ALTER TABLE `tbl_widgets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_widget_position`
--
ALTER TABLE `tbl_widget_position`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `sourcemessage`
--
ALTER TABLE `sourcemessage`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `tbl_access_tokens`
--
ALTER TABLE `tbl_access_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `tbl_article_details`
--
ALTER TABLE `tbl_article_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `tbl_authorization_codes`
--
ALTER TABLE `tbl_authorization_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `tbl_categories`
--
ALTER TABLE `tbl_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `tbl_comments`
--
ALTER TABLE `tbl_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT for table `tbl_css`
--
ALTER TABLE `tbl_css`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `tbl_feedback`
--
ALTER TABLE `tbl_feedback`
  MODIFY `reference_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `tbl_feedback_details`
--
ALTER TABLE `tbl_feedback_details`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;
--
-- AUTO_INCREMENT for table `tbl_feedback_files`
--
ALTER TABLE `tbl_feedback_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `tbl_feedback_reply`
--
ALTER TABLE `tbl_feedback_reply`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `tbl_form_data`
--
ALTER TABLE `tbl_form_data`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=361;
--
-- AUTO_INCREMENT for table `tbl_form_files`
--
ALTER TABLE `tbl_form_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT for table `tbl_form_submit`
--
ALTER TABLE `tbl_form_submit`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=94;
--
-- AUTO_INCREMENT for table `tbl_menu`
--
ALTER TABLE `tbl_menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `tbl_menu_page`
--
ALTER TABLE `tbl_menu_page`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
--
-- AUTO_INCREMENT for table `tbl_profile_details`
--
ALTER TABLE `tbl_profile_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `tbl_rating_profile`
--
ALTER TABLE `tbl_rating_profile`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `tbl_rating_profile_details`
--
ALTER TABLE `tbl_rating_profile_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `tbl_settings`
--
ALTER TABLE `tbl_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;
--
-- AUTO_INCREMENT for table `tbl_sliders`
--
ALTER TABLE `tbl_sliders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `tbl_slider_image`
--
ALTER TABLE `tbl_slider_image`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;
--
-- AUTO_INCREMENT for table `tbl_subscriptions`
--
ALTER TABLE `tbl_subscriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `tbl_template_widget`
--
ALTER TABLE `tbl_template_widget`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=270;
--
-- AUTO_INCREMENT for table `tbl_testimonials`
--
ALTER TABLE `tbl_testimonials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `tbl_themes`
--
ALTER TABLE `tbl_themes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `tbl_twig`
--
ALTER TABLE `tbl_twig`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;
--
-- AUTO_INCREMENT for table `tbl_user`
--
ALTER TABLE `tbl_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `tbl_web_hook`
--
ALTER TABLE `tbl_web_hook`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `tbl_web_hook_email`
--
ALTER TABLE `tbl_web_hook_email`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `tbl_widgets`
--
ALTER TABLE `tbl_widgets`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `message`
--
ALTER TABLE `message`
  ADD CONSTRAINT `message_ibfk_1` FOREIGN KEY (`id`) REFERENCES `sourcemessage` (`id`) ON DELETE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
