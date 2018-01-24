insert into tbl_intelligence_category (category) values ('Classification');
insert into tbl_intelligence_category (category) values ('Prevision');
insert into tbl_intelligence_category (category) values ('Clustering');

insert into tbl_intelligence_data_type (type) values ('Sheet');
insert into tbl_intelligence_data_type (type) values ('Image');
insert into tbl_intelligence_data_type (type) values ('Sound');
insert into tbl_intelligence_data_type (type) values ('Json');

insert into tbl_log_type (type) values ('Creation');
insert into tbl_log_type (type) values ('Use');
insert into tbl_log_type (type) values ('Modification');

insert into tbl_user (name, email, password, username, id_resp_inc) values ('admin', 'admin@admin.com', 'admin', 'admin', 0);
