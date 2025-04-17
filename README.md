# Bike Rides API

🖥 [API][ApiRepo] | 📱 [App][AppRepo] | 💼 [Admin][AdminRepo] | 🏗 [Infrastructure][InfraRepo]

This project aims to demonstrate the use of DDD, CQRS, Event Sourcing and Hexagonal Architecture to build an event-driven SOA for a fictional Bike Rides business.
The project is loosely based on and inspired by Lime Bikes, since I've been using them to get around the city for some time and have really come to enjoy this mode of transport!

In order to flex the platform in a semi-realistic way there's [an app][AppRepo] built using React Native and [an admin client][AdminRepo] built using React Admin.

Each API is deployed on AWS Lambda using GitHub Actions and the Serverless Framework.
Supporting infrastructure can be found in the [bike-rides-infra][InfraRepo] repository and the following diagram gives a rough overview of the layout within AWS.

![image](https://github.com/user-attachments/assets/359930f7-5422-4e91-b234-7c56a344a254)

[ApiRepo]: https://github.com/tomcant/bike-rides-api
[AppRepo]: https://github.com/tomcant/bike-rides-app
[AdminRepo]: https://github.com/tomcant/bike-rides-admin
[InfraRepo]: https://github.com/tomcant/bike-rides-infra
