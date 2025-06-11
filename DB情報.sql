-- 初期化（バルス）
TRUNCATE TABLE t_ellena_cataloggift2507;
select setval ('t_ellena_cataloggift2507_entry_sid_seq', 1, false);

-- 応募
CREATE SEQUENCE t_ellena_cataloggift2507_entry_sid_seq;

CREATE TABLE "public"."t_ellena_cataloggift2507" (
    "entry_sid" bigint NOT NULL DEFAULT nextval('t_ellena_cataloggift2507_entry_sid_seq'::regclass),
    "entry_ts" timestamp without time zone,
    "user_devi_type" character varying(8),
    "receipt_num" character varying(16),
    "c_type" character varying(8),
    "c_id" character varying(16),
    "entry_place" character varying(16),
    "staff_id" character varying(16),
    "c_item" character varying(128),
    "c_item_jan" character varying(16),
    "deliv_date" character varying(16),
    "name_sei" character varying(64),
    "name_mei" character varying(64),
    "kana_sei" character varying(64),
    "kana_mei" character varying(64),
    "zipcode" character varying(8),
    "pref_name" character varying(16),
    "address1" character varying(256),
    "address2" character varying(256),
    "telphone" character varying(16),
    "sub_name_sei" character varying(64),
    "sub_name_mei" character varying(64),
    "sub_kana_sei" character varying(64),
    "sub_kana_mei" character varying(64),
    "sub_zipcode" character varying(8),
    "sub_pref_name" character varying(16),
    "sub_address1" character varying(256),
    "sub_address2" character varying(256),
    "sub_telphone" character varying(16),
    "noshi" character varying(8),
    "noshi_type" character varying(8),
    "noshi_name" text,
    "email" character varying(512),
    "is_del" numeric(1,0) NOT NULL DEFAULT 0,
    "reg_ts" timestamp without time zone NOT NULL,
    "reg_ope" character varying(64) NOT NULL,
    "mod_ts" timestamp without time zone NOT NULL,
    "mod_ope" character varying(64) NOT NULL,
    "user_id" character(256),
    "regi_no" character(8),
    "receipt_no" character(8),
	"order_number" character(8)
) WITHOUT OIDS;

-- Comment

COMMENT ON TABLE "public"."t_ellena_cataloggift2507" IS 'エレナ 夏カタログギフト2025 応募収集用';
COMMENT ON COLUMN "t_ellena_cataloggift2507"."entry_sid"  IS '応募 シーケンシャル番号';
COMMENT ON COLUMN "t_ellena_cataloggift2507"."entry_ts"  IS '応募日時(WEBフォーム設置サーバー上の時刻を記録)';
COMMENT ON COLUMN "t_ellena_cataloggift2507"."user_devi_type"  IS '応募デバイス';
COMMENT ON COLUMN "t_ellena_cataloggift2507"."receipt_num"  IS '受付番号';
COMMENT ON COLUMN "t_ellena_cataloggift2507"."c_type"  IS 'カタログタイプ';
COMMENT ON COLUMN "t_ellena_cataloggift2507"."c_id"  IS 'お客様のIDコード';
COMMENT ON COLUMN "t_ellena_cataloggift2507"."entry_place"  IS '店頭申し込み';
COMMENT ON COLUMN "t_ellena_cataloggift2507"."staff_id"  IS 'スタッフコード';
COMMENT ON COLUMN "t_ellena_cataloggift2507"."c_item"  IS '希望商品';
COMMENT ON COLUMN "t_ellena_cataloggift2507"."c_item_jan"  IS '希望商品 JANコード';
COMMENT ON COLUMN "t_ellena_cataloggift2507"."deliv_date"  IS '希望配達日';
COMMENT ON COLUMN "t_ellena_cataloggift2507"."name_sei"  IS '名前　姓';
COMMENT ON COLUMN "t_ellena_cataloggift2507"."name_mei"  IS '名前　名';
COMMENT ON COLUMN "t_ellena_cataloggift2507"."kana_sei"  IS '名前　セイ';
COMMENT ON COLUMN "t_ellena_cataloggift2507"."kana_mei"  IS '名前　メイ';
COMMENT ON COLUMN "t_ellena_cataloggift2507"."zipcode"  IS '郵便番号';
COMMENT ON COLUMN "t_ellena_cataloggift2507"."pref_name"  IS '住所　都道府県';
COMMENT ON COLUMN "t_ellena_cataloggift2507"."address1"  IS '住所　市区町村';
COMMENT ON COLUMN "t_ellena_cataloggift2507"."address2"  IS '住所　番地・建物名';
COMMENT ON COLUMN "t_ellena_cataloggift2507"."telphone"  IS '電話番号';
COMMENT ON COLUMN "t_ellena_cataloggift2507"."sub_name_sei"  IS '(お届け先)名前　姓';
COMMENT ON COLUMN "t_ellena_cataloggift2507"."sub_name_mei"  IS '(お届け先)名前　名';
COMMENT ON COLUMN "t_ellena_cataloggift2507"."sub_kana_sei"  IS '(お届け先)名前　セイ';
COMMENT ON COLUMN "t_ellena_cataloggift2507"."sub_kana_mei"  IS '(お届け先)名前　メイ';
COMMENT ON COLUMN "t_ellena_cataloggift2507"."sub_zipcode"  IS '(お届け先)郵便番号';
COMMENT ON COLUMN "t_ellena_cataloggift2507"."sub_pref_name"  IS '(お届け先)住所　都道府県';
COMMENT ON COLUMN "t_ellena_cataloggift2507"."sub_address1"  IS '(お届け先)住所　市区町村';
COMMENT ON COLUMN "t_ellena_cataloggift2507"."sub_address2"  IS '(お届け先)住所　番地・建物名';
COMMENT ON COLUMN "t_ellena_cataloggift2507"."sub_telphone"  IS '(お届け先)電話番号';
COMMENT ON COLUMN "t_ellena_cataloggift2507"."noshi"  IS 'のし 有無';
COMMENT ON COLUMN "t_ellena_cataloggift2507"."noshi_type"  IS 'のし 種別';
COMMENT ON COLUMN "t_ellena_cataloggift2507"."noshi_name"  IS 'のし 名入れ';
COMMENT ON COLUMN "t_ellena_cataloggift2507"."email"  IS 'メールアドレス';
COMMENT ON COLUMN "t_ellena_cataloggift2507"."is_del"  IS '削除フラグ';
COMMENT ON COLUMN "t_ellena_cataloggift2507"."reg_ts"  IS '登録日時';
COMMENT ON COLUMN "t_ellena_cataloggift2507"."reg_ope"  IS '登録作業者';
COMMENT ON COLUMN "t_ellena_cataloggift2507"."mod_ts"  IS '変更日時';
COMMENT ON COLUMN "t_ellena_cataloggift2507"."mod_ope"  IS '変更作業者';
COMMENT ON COLUMN "t_ellena_cataloggift2507"."user_id"  IS 'ユーザーID';
COMMENT ON COLUMN "t_ellena_cataloggift2507"."regi_no"  IS 'レジ番号（カタログギフト用）';
COMMENT ON COLUMN "t_ellena_cataloggift2507"."receipt_no"  IS 'レシート番号（カタログギフト用）';
COMMENT ON COLUMN "t_ellena_cataloggift2507"."order_number"  IS '商品注文数';

==========================================
削除
==========================================
DROP TABLE t_ellena_cataloggift2507;
DROP SEQUENCE t_ellena_cataloggift2507_entry_sid_seq;



    pack character varying(8),
COMMENT ON COLUMN t_ellena_cataloggift2507.pack IS '包装 有無';



ALTER TABLE t_ellena_cataloggift2507 DROP COLUMN pack;


//190319 Add
ALTER TABLE t_urawacamp190316 ADD agree character varying(16);
COMMENT ON COLUMN t_urawacamp190316.agree IS '肖像権使用同意について';

//231205 Update
ALTER TABLE t_ellena_cataloggift2507 ALTER COLUMN q01 TYPE character varying(128);
