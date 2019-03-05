var FiltersEnabled = 0; // if your not going to use transitions or filters in any of the tips set this to 0
var spacer="&nbsp; &nbsp; &nbsp; ";

// email notifications to admin
notifyAdminNewMembers0Tip=["", spacer+"No email notifications to admin."];
notifyAdminNewMembers1Tip=["", spacer+"Notify admin only when a new member is waiting for approval."];
notifyAdminNewMembers2Tip=["", spacer+"Notify admin for all new sign-ups."];

// visitorSignup
visitorSignup0Tip=["", spacer+"If this option is selected, visitors will not be able to join this group unless the admin manually moves them to this group from the admin area."];
visitorSignup1Tip=["", spacer+"If this option is selected, visitors can join this group but will not be able to sign in unless the admin approves them from the admin area."];
visitorSignup2Tip=["", spacer+"If this option is selected, visitors can join this group and will be able to sign in instantly with no need for admin approval."];

// buses table
buses_addTip=["",spacer+"This option allows all members of the group to add records to the 'Buses' table. A member who adds a record to the table becomes the 'owner' of that record."];

buses_view0Tip=["",spacer+"This option prohibits all members of the group from viewing any record in the 'Buses' table."];
buses_view1Tip=["",spacer+"This option allows each member of the group to view only his own records in the 'Buses' table."];
buses_view2Tip=["",spacer+"This option allows each member of the group to view any record owned by any member of the group in the 'Buses' table."];
buses_view3Tip=["",spacer+"This option allows each member of the group to view all records in the 'Buses' table."];

buses_edit0Tip=["",spacer+"This option prohibits all members of the group from modifying any record in the 'Buses' table."];
buses_edit1Tip=["",spacer+"This option allows each member of the group to edit only his own records in the 'Buses' table."];
buses_edit2Tip=["",spacer+"This option allows each member of the group to edit any record owned by any member of the group in the 'Buses' table."];
buses_edit3Tip=["",spacer+"This option allows each member of the group to edit any records in the 'Buses' table, regardless of their owner."];

buses_delete0Tip=["",spacer+"This option prohibits all members of the group from deleting any record in the 'Buses' table."];
buses_delete1Tip=["",spacer+"This option allows each member of the group to delete only his own records in the 'Buses' table."];
buses_delete2Tip=["",spacer+"This option allows each member of the group to delete any record owned by any member of the group in the 'Buses' table."];
buses_delete3Tip=["",spacer+"This option allows each member of the group to delete any records in the 'Buses' table."];

// seats table
seats_addTip=["",spacer+"This option allows all members of the group to add records to the 'Seats' table. A member who adds a record to the table becomes the 'owner' of that record."];

seats_view0Tip=["",spacer+"This option prohibits all members of the group from viewing any record in the 'Seats' table."];
seats_view1Tip=["",spacer+"This option allows each member of the group to view only his own records in the 'Seats' table."];
seats_view2Tip=["",spacer+"This option allows each member of the group to view any record owned by any member of the group in the 'Seats' table."];
seats_view3Tip=["",spacer+"This option allows each member of the group to view all records in the 'Seats' table."];

seats_edit0Tip=["",spacer+"This option prohibits all members of the group from modifying any record in the 'Seats' table."];
seats_edit1Tip=["",spacer+"This option allows each member of the group to edit only his own records in the 'Seats' table."];
seats_edit2Tip=["",spacer+"This option allows each member of the group to edit any record owned by any member of the group in the 'Seats' table."];
seats_edit3Tip=["",spacer+"This option allows each member of the group to edit any records in the 'Seats' table, regardless of their owner."];

seats_delete0Tip=["",spacer+"This option prohibits all members of the group from deleting any record in the 'Seats' table."];
seats_delete1Tip=["",spacer+"This option allows each member of the group to delete only his own records in the 'Seats' table."];
seats_delete2Tip=["",spacer+"This option allows each member of the group to delete any record owned by any member of the group in the 'Seats' table."];
seats_delete3Tip=["",spacer+"This option allows each member of the group to delete any records in the 'Seats' table."];

// availability table
availability_addTip=["",spacer+"This option allows all members of the group to add records to the 'Availability' table. A member who adds a record to the table becomes the 'owner' of that record."];

availability_view0Tip=["",spacer+"This option prohibits all members of the group from viewing any record in the 'Availability' table."];
availability_view1Tip=["",spacer+"This option allows each member of the group to view only his own records in the 'Availability' table."];
availability_view2Tip=["",spacer+"This option allows each member of the group to view any record owned by any member of the group in the 'Availability' table."];
availability_view3Tip=["",spacer+"This option allows each member of the group to view all records in the 'Availability' table."];

availability_edit0Tip=["",spacer+"This option prohibits all members of the group from modifying any record in the 'Availability' table."];
availability_edit1Tip=["",spacer+"This option allows each member of the group to edit only his own records in the 'Availability' table."];
availability_edit2Tip=["",spacer+"This option allows each member of the group to edit any record owned by any member of the group in the 'Availability' table."];
availability_edit3Tip=["",spacer+"This option allows each member of the group to edit any records in the 'Availability' table, regardless of their owner."];

availability_delete0Tip=["",spacer+"This option prohibits all members of the group from deleting any record in the 'Availability' table."];
availability_delete1Tip=["",spacer+"This option allows each member of the group to delete only his own records in the 'Availability' table."];
availability_delete2Tip=["",spacer+"This option allows each member of the group to delete any record owned by any member of the group in the 'Availability' table."];
availability_delete3Tip=["",spacer+"This option allows each member of the group to delete any records in the 'Availability' table."];

// bookings table
bookings_addTip=["",spacer+"This option allows all members of the group to add records to the 'Bookings' table. A member who adds a record to the table becomes the 'owner' of that record."];

bookings_view0Tip=["",spacer+"This option prohibits all members of the group from viewing any record in the 'Bookings' table."];
bookings_view1Tip=["",spacer+"This option allows each member of the group to view only his own records in the 'Bookings' table."];
bookings_view2Tip=["",spacer+"This option allows each member of the group to view any record owned by any member of the group in the 'Bookings' table."];
bookings_view3Tip=["",spacer+"This option allows each member of the group to view all records in the 'Bookings' table."];

bookings_edit0Tip=["",spacer+"This option prohibits all members of the group from modifying any record in the 'Bookings' table."];
bookings_edit1Tip=["",spacer+"This option allows each member of the group to edit only his own records in the 'Bookings' table."];
bookings_edit2Tip=["",spacer+"This option allows each member of the group to edit any record owned by any member of the group in the 'Bookings' table."];
bookings_edit3Tip=["",spacer+"This option allows each member of the group to edit any records in the 'Bookings' table, regardless of their owner."];

bookings_delete0Tip=["",spacer+"This option prohibits all members of the group from deleting any record in the 'Bookings' table."];
bookings_delete1Tip=["",spacer+"This option allows each member of the group to delete only his own records in the 'Bookings' table."];
bookings_delete2Tip=["",spacer+"This option allows each member of the group to delete any record owned by any member of the group in the 'Bookings' table."];
bookings_delete3Tip=["",spacer+"This option allows each member of the group to delete any records in the 'Bookings' table."];

// routes table
routes_addTip=["",spacer+"This option allows all members of the group to add records to the 'Routes' table. A member who adds a record to the table becomes the 'owner' of that record."];

routes_view0Tip=["",spacer+"This option prohibits all members of the group from viewing any record in the 'Routes' table."];
routes_view1Tip=["",spacer+"This option allows each member of the group to view only his own records in the 'Routes' table."];
routes_view2Tip=["",spacer+"This option allows each member of the group to view any record owned by any member of the group in the 'Routes' table."];
routes_view3Tip=["",spacer+"This option allows each member of the group to view all records in the 'Routes' table."];

routes_edit0Tip=["",spacer+"This option prohibits all members of the group from modifying any record in the 'Routes' table."];
routes_edit1Tip=["",spacer+"This option allows each member of the group to edit only his own records in the 'Routes' table."];
routes_edit2Tip=["",spacer+"This option allows each member of the group to edit any record owned by any member of the group in the 'Routes' table."];
routes_edit3Tip=["",spacer+"This option allows each member of the group to edit any records in the 'Routes' table, regardless of their owner."];

routes_delete0Tip=["",spacer+"This option prohibits all members of the group from deleting any record in the 'Routes' table."];
routes_delete1Tip=["",spacer+"This option allows each member of the group to delete only his own records in the 'Routes' table."];
routes_delete2Tip=["",spacer+"This option allows each member of the group to delete any record owned by any member of the group in the 'Routes' table."];
routes_delete3Tip=["",spacer+"This option allows each member of the group to delete any records in the 'Routes' table."];

// customers table
customers_addTip=["",spacer+"This option allows all members of the group to add records to the 'Customers' table. A member who adds a record to the table becomes the 'owner' of that record."];

customers_view0Tip=["",spacer+"This option prohibits all members of the group from viewing any record in the 'Customers' table."];
customers_view1Tip=["",spacer+"This option allows each member of the group to view only his own records in the 'Customers' table."];
customers_view2Tip=["",spacer+"This option allows each member of the group to view any record owned by any member of the group in the 'Customers' table."];
customers_view3Tip=["",spacer+"This option allows each member of the group to view all records in the 'Customers' table."];

customers_edit0Tip=["",spacer+"This option prohibits all members of the group from modifying any record in the 'Customers' table."];
customers_edit1Tip=["",spacer+"This option allows each member of the group to edit only his own records in the 'Customers' table."];
customers_edit2Tip=["",spacer+"This option allows each member of the group to edit any record owned by any member of the group in the 'Customers' table."];
customers_edit3Tip=["",spacer+"This option allows each member of the group to edit any records in the 'Customers' table, regardless of their owner."];

customers_delete0Tip=["",spacer+"This option prohibits all members of the group from deleting any record in the 'Customers' table."];
customers_delete1Tip=["",spacer+"This option allows each member of the group to delete only his own records in the 'Customers' table."];
customers_delete2Tip=["",spacer+"This option allows each member of the group to delete any record owned by any member of the group in the 'Customers' table."];
customers_delete3Tip=["",spacer+"This option allows each member of the group to delete any records in the 'Customers' table."];

/*
	Style syntax:
	-------------
	[TitleColor,TextColor,TitleBgColor,TextBgColor,TitleBgImag,TextBgImag,TitleTextAlign,
	TextTextAlign,TitleFontFace,TextFontFace, TipPosition, StickyStyle, TitleFontSize,
	TextFontSize, Width, Height, BorderSize, PadTextArea, CoordinateX , CoordinateY,
	TransitionNumber, TransitionDuration, TransparencyLevel ,ShadowType, ShadowColor]

*/

toolTipStyle=["white","#00008B","#000099","#E6E6FA","","images/helpBg.gif","","","","\"Trebuchet MS\", sans-serif","","","","3",400,"",1,2,10,10,51,1,0,"",""];

applyCssFilter();
