# INFOH303-Horeca
Project for course INFO-H-303: Databases at Ecole Polytechnique de Bruxelles.

# Structure
## Schema
####Establishment
```
int id
string Name
Address
	string Street
	int Number
	int PC
	string City
Coordinates
	double Longitude
	double Latitude
string Phone
string URL
date CreationDate
int CreationUser
```

####Restaurant::Establishment
```
PriceRange
	double LowerBound
	double UpperBound
int Seats
bool DoesTakeAway
bool DoesDelivery
tba HalfDaysOff
```

####Bar::Establishment
```
bool AllowsSmoking
bool DoesCatering
```

####Hotel::Establishment
```
int Stars
int Rooms
double ExamplePrice
```

####User
```
int id
string Name
string Mail
string Password
date JoinDate
int Type (0: regular user, 1: admin, etc)
```

####Comment
```
int id
int Score
string Text
date Date
```

####UserTagsEstablishment
```
int id
int User
int Tag
int Establishment
```

####Tags
```
int id
string Name
```
