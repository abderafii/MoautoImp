-- we start by creating the different tables that we need, these tables are hosted online on railway to allow for team modifications
--Not all tables are used due to lack of time and expertise concerning programing languages
create table Monthly_Summary(
 MonthID    Serial PRIMARY KEY,
 MonthName  varchar(35) not null
);

create table Monthly_Summary_Line(
 MonthID int,
 LineID  int,
 productID   int,
 MonthlyQuantity     int not null DEFAULT 0,
 MonthlyTotalProfit     int not null Default 0,
 PRIMARY KEY (MonthID, LineID),
 UNIQUE(MonthID, productID),
 CONSTRAINT Has FOREIGN KEY (MonthID) REFERENCES Monthly_Summary(MonthID)
                      ON UPDATE CASCADE ON DELETE CASCADE,
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
 SupPhone char(8) not null,
 DebtToSup int not null DEFAULT 0
);

create table Representative(
 RepID Serial PRIMARY KEY,
 RepFname varchar(15) not null,
 RepLname varchar(15) not null,
 RepPhone char(8) not null,
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
 CusPhone char(15) unique not null,
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