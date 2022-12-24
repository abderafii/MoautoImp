-- we start by creating the different tables that we need, these tables are hosted online on railway to allow for team modifications
--Not all tables are used due to lack of time and expertise concerning programing languages

create table Profit_Line(
 ProfitLineID  serial PRIMARY KEY,
 createdate date,
 productID   int,
 ProdTotalProfit     int not null Default 0,
 CONSTRAINT Appears FOREIGN KEY (ProductID) REFERENCES Product(ProductID)
                      ON UPDATE CASCADE ON DELETE CASCADE
);

create table Product(
 ProductID Serial PRIMARY KEY,
 ProductDescription varchar(25) not null,
 SupID int,
 ProductCurrentPrice decimal(8, 2) not null,
 ProductAcquisitionPrice decimal(8, 2) not null,
 ProductQuantityOnHand int not null,
 ProductMinQuantityOnHand int not null,
 ProductDiscount decimal(2, 2) not null DEFAULT 0, 
 CONSTRAINT Provides FOREIGN KEY (SupID) REFERENCES Supplier(SupID)
                      ON UPDATE CASCADE ON DELETE CASCADE
);

create table Supplier(
 SupID Serial PRIMARY KEY,
 SupName varchar(25) not null,
 SupAddress varchar(25),
 SupPhone varchar(15) not null,
 DebtToSup int not null DEFAULT 0
);

create table Representative(
 RepID Serial PRIMARY KEY,
 RepFname varchar(15) not null,
 RepLname varchar(15) not null,
 RepPhone varchar(15) not null,
 SupID int,
 CONSTRAINT Hires FOREIGN KEY (SupID) REFERENCES Supplier(SupID)
                      ON UPDATE CASCADE ON DELETE CASCADE
);

create table Customer(
 CusID Serial PRIMARY KEY,
 CusFname varchar(15) not null,
 CusLname varchar(15) not null,
 Cusemail varchar(255) unique not null,
 CusPassword varchar(255) not null,
 CusPhone varchar(15) unique not null,
 CusBalance decimal(8, 2) DEFAULT 0,
 CusAddress varchar(25)
);

create table Shopping_Cart(
 CartID SERIAL PRIMARY KEY,
 CusID int, 
 CONSTRAINT Has4 FOREIGN KEY (CusID) REFERENCES Customer(CusID)
                      ON UPDATE CASCADE ON DELETE CASCADE
);

create table Line(
 LineSerial SERIAL PRIMARY KEY,
 InvoiceID int,
 CartID int,
 ProductID int,
 LineQuantity int not null DEFAULT 1,
 LineHistoricalPrice decimal(8, 2) not null,
 LinePaymentType char(1),
 LineTotalAmount decimal(8,2) DEFAULT 0 not null,
 CONSTRAINT ChkPayType CHECK (LinePaymentType in ('C', 'O')),
 CONSTRAINT Appears FOREIGN KEY (ProductID) REFERENCES Product(ProductID)
                      ON UPDATE CASCADE ON DELETE CASCADE,
 CONSTRAINT Has2 FOREIGN KEY (CartID) REFERENCES Shopping_Cart(CartID)
                      ON UPDATE CASCADE ON DELETE CASCADE,
 CONSTRAINT Has3 FOREIGN KEY (InvoiceID) REFERENCES Invoice(InvoiceID)
                      ON UPDATE CASCADE ON DELETE CASCADE
);

create table Invoice(
 InvoiceID  serial,
 InvoiceType char(1) not null,
 CONSTRAINT invpkey PRIMARY KEY(InvoiceID),
 CONSTRAINT ChkInvcType CHECK (InvoiceType in ('D', 'O', 'C'))
);

create table Delivery_Invoice(
 ProductsDeliveryDate date not null,
 InvoiceType char(1) Default 'D',
 CONSTRAINT invidprimekeyD PRIMARY KEY(InvoiceID, InvoiceType),
 CONSTRAINT ChkInvcTypeD CHECK (InvoiceType in ('D'))
) INHERITS (Invoice);

create table Customer_Invoice(
 InvoiceDate date not null,
 CusID int,
 CONSTRAINT BelongsTo FOREIGN KEY (CusID) REFERENCES Customer(CusID)
                      ON UPDATE CASCADE ON DELETE CASCADE,
 InvoiceType char(1) Default 'C',
 CONSTRAINT invidprimekeyC PRIMARY KEY(InvoiceID, InvoiceType),
 CONSTRAINT ChkInvcTypeC CHECK (InvoiceType in ('C'))
) INHERITS (Invoice);

create table Order_Invoice(
 ProductsDeliveryDate date not null,
 ProductsOrderDate date not null,
 InvoiceType char(1) Default 'O',
 CONSTRAINT invidprimekeyO PRIMARY KEY(InvoiceID, InvoiceType),
 CONSTRAINT ChkInvcTypeO CHECK (InvoiceType in ('O'))
) INHERITS (Invoice);

--we create triggers to ensure that our database is up to date
--the first trigger create a shopping cart for the customer
create function trig1cuscart() returns trigger
language plpgsql
as 
$$ 
Begin
insert into Shopping_Cart (cusID) values (new.CusID); 
return new;
End;
$$;



Create trigger cuscart 
after insert on customer for each row execute function trig1cuscart();

--the second trigger creates a customer invoice
create function trig2cusinvoice() returns trigger
language plpgsql
as 
$$ 
Begin
insert into Customer_Invoice (cusID, InvoiceDate) values (new.CusID, current_date); 
return new;
End;
$$;

Create trigger cusinvoice 
after insert on customer for each row execute function trig2cusinvoice();


--populating the database using only 1 entry just to test the online database and it is working!!
insert into customer (cusemail, cuspassword, cuslname, cusfname, cusphone) values ('aaa@adui.ma', '52222', 'hamza', 'hamid', '20000366');

--the third trigger to update the line table total price for each row after each purchase
create function triglinebalance() returns trigger
language plpgsql
as 
$$ 
declare 
total int;
Begin
total = new.LineHistoricalPrice * new.LineQuantity;
Update line 
set LineTotalAmount = total where LineSerial=new.LineSerial; 
return new;
End;
$$;

Create trigger linebalance 
after insert on line for each row execute function triglinebalance();

----the fourth trigger to update the customer table balance for each row after each purchase
create or replace function trig4() returns trigger
language plpgsql
as 
$$ 
declare 
total int;
Begin
total = new.LineHistoricalPrice * new.LineQuantity;
Update customer as c 
set CusBalance = CusBalance + total from shopping_cart s where c.cusID = s.cusID and s.CartID = new.CartID;
return new;
End;
$$;

Create trigger cusbalancetrig 
after insert on line for each row execute function trig4();

--create trigger in order to create a row in the invoice table since the invoice table does not 
--take in consideration the invoices created by his subtypes.
create or replace function trig5() returns trigger
language plpgsql
as 
$$ 
Begin
Insert into invoice (invoiceId, invoicetype) values (new.invoiceid, new.invoicetype);
return new;
End;
$$;

Create trigger cusinv
after insert on Customer_Invoice for each row execute function trig5();

--create a trigger to calculate the profit for each product;
create or replace function trig6() returns trigger
language plpgsql
as 
$$ 
declare 
total int;
Begin
total = (new.LineHistoricalPrice - (select ProductAcquisitionPrice from product where productid=new.productid)) * new.LineQuantity;
Insert into Profit_Line(createdate ,productid, ProdTotalProfit) values (current_date ,new.productid, total);
return new;
End;
$$;

Create trigger createmonthsumline
after insert on Line for each row execute function trig6();

--create trigger to update quantity on hand
create or replace function trig7() returns trigger
language plpgsql
as 
$$ 
Begin
Update Product
set ProductQuantityOnHand = ProductQuantityOnHand - new.LineQuantity
where productid = new.productid;
return new;
End;
$$;

Create trigger updatequantonhand
after insert on Line for each row execute function trig7();

--create trigger to check the quantity on hand and the min quantity on hand
create or replace function trig8() returns trigger
language plpgsql
as 
$$ 
Begin
IF new.ProductQuantityOnHand < new.ProductMinQuantityOnHand THEN
   Update product 
   set ProductQuantityOnHand = new.ProductQuantityOnHand + 5 where productID = new.productID;
   update supplier 
   set DebtToSup = DebtToSup + 5*new.ProductAcquisitionPrice where SupID = new.SupID;
END IF;
return new;
End;
$$;

Create trigger orderprod
after update on product for each row execute function trig8();

--Populating the Database
Insert into product 
(ProductDescription, supid, ProductCurrentPrice, ProductAcquisitionPrice, ProductQuantityOnHand,
 ProductMinQuantityOnHand, ProductDiscount) values 
 ('Head Lamp for Dacia', 5, 450, 400, 15, 5, 00),
 ('Front Wiper 650mm', 2, 100, 70, 12, 5, 00),
 ('Kit Timing Belt Dacia', 3, 800, 650, 10, 5, 00),
 ('Dacia Dokker Oil Filter', 2, 180, 150, 12, 5, 00),
 ('Diesel Filter RENAULT Clio', 4, 169, 140, 14, 5, 00),
 ('Helix Ultra 5w 40', 1, 200, 190, 15, 5, 00),
 ('Air Filter Clio', 3, 80, 60, 20, 5, 00),
 ('Activa 9000 5w-40 1 Liter', 6, 40, 20, 20, 5, 00);

Insert into supplier
(supname, supaddress, supphone, debttosup) values
 ('Filtron', 'Casablanca Hay Riad', '0625553696'),
 ('Jiangsu Reshine', 'China Beijing', '+8625368965'), 
 ('Total', 'Casablanca Derb Ghalef', '0525553265'),
 ('Shell', 'Casablanca Anfa Place', '0525557898'),
 ('Renault', 'Casablanca Ain Sbaa', '0525553654'),
 ('Bosch', 'Casablanca Maarif', '0525558744');

Insert into Representative
(RepFname, RepLname, RepPhone, supid) values
 ('Ahmed', 'Zianni', '0637384185', 1),
 ('Hamza', 'Arafat', '0658693354', 2),
 ('Hamid', 'Lhlou', '0748996355', 3),
 ('Hamada', 'Laala', '0698875521', 4),
 ('Ali', 'Benhadou', '0635142589', 5),
 ('Riad', 'Senhaji', '0635994885', 6);
 
