SELECT
    `p_post`.`id` AS `post_id`,
    `p_post`.`title` AS `post_title`,
    `p_post`.`text` AS `post_text`,
    `p_post`.`PWBversion` AS `post_PWBversion`
  FROM `post` AS `p_post`
  WHERE EXISTS
    (SELECT
        `tag_posttag_tag`.`id` AS `tag_id`
       FROM `posttag` AS ` tag_posttag`, `tag` AS `tag_posttag_tag`
       WHERE `tag_posttag`.`post` = `p_post`.`id`
          AND `tag_posttag`.`tag` = `tag_posttag_tag`.`id`
          AND `tag_posttag_tag`.`name` = $tag
    )

