/** 
 * Static master data for defining conditions supported by Rules to define dynamic routing
 * 
 */

 INSERT INTO System.condition_tbl (id,name,description,type) values (1,'Amount','Amount value ','s');
 INSERT INTO System.condition_tbl (id,name,description,type) values (2,'Currency','Currency numeric ISO 4217 code ','s');
 INSERT INTO System.condition_tbl (id,name,description,type) values (3,'Binrange','Card bin range','s');
 INSERT INTO System.condition_tbl (id,name,description,type) values (4,'Card Scheme','Card Network','s');
 INSERT INTO System.condition_tbl (id,name,description,type) values (5,'Volume','Transaction volume','d');
 INSERT INTO System.condition_tbl (id,name,description,type) values (6,'Product','Type of product e.g Anciliary, Insurance etc','s');
