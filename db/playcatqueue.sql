DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs`  (
  `jid` int(0) UNSIGNED NOT NULL AUTO_INCREMENT,
  `timerid` int(0) NOT NULL DEFAULT -1,
  `iconicid` int(0) NOT NULL DEFAULT -1,
  `data` blob NULL,
  `expiration` int(0) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`jid`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;
