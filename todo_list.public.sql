/*
 Navicat Premium Dump SQL

 Source Server         : LOCAL POSTGRES 15
 Source Server Type    : PostgreSQL
 Source Server Version : 150001 (150001)
 Source Host           : localhost:5432
 Source Catalog        : todo_list
 Source Schema         : public

 Target Server Type    : PostgreSQL
 Target Server Version : 150001 (150001)
 File Encoding         : 65001

 Date: 04/10/2025 14:17:43
*/


-- ----------------------------
-- Sequence structure for todo_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."todo_id_seq";
CREATE SEQUENCE "public"."todo_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 2147483647
START 1
CACHE 1;

-- ----------------------------
-- Table structure for todo
-- ----------------------------
DROP TABLE IF EXISTS "public"."todo";
CREATE TABLE "public"."todo" (
  "id" int4 NOT NULL DEFAULT nextval('todo_id_seq'::regclass),
  "task" varchar(255) COLLATE "pg_catalog"."default",
  "description" text COLLATE "pg_catalog"."default",
  "created_at" timestamp(6) DEFAULT CURRENT_TIMESTAMP,
  "completed" bool NOT NULL DEFAULT false
)
;

-- ----------------------------
-- Records of todo
-- ----------------------------

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."todo_id_seq"
OWNED BY "public"."todo"."id";
SELECT setval('"public"."todo_id_seq"', 16, true);

-- ----------------------------
-- Primary Key structure for table todo
-- ----------------------------
ALTER TABLE "public"."todo" ADD CONSTRAINT "todo_pkey" PRIMARY KEY ("id");
