alter table User
    add column multi_client int(1),
    add column name varchar(80),
    add column business_name varchar(80),
    add column phone varchar(30),
    add column address varchar(120),
    add column bill_business_name varchar(80),
    add column bill_contact_name varchar(80),
    add column bill_email varchar(50),
    add column bill_phone varchar(50),
    add column bill_address varchar(120),
    add column date_create datetime;

