# Bike Rides API

This project aims to demonstrate the use of DDD, CQRS, event sourcing and hexagonal architecture to build an API for a bike rides business.
The project is loosely based on and inspired by Lime Bikes, since I've been using them to get around the city for some time now and have really come to enjoy this mode of transport!

In order to flex the API from a semi-realistic client there's [an accompanying Android app](https://github.com/tomcant/bike-rides-app) built using React Native.

The API is deployed on AWS Lambda using GitHub Actions and the Serverless Framework.
Supporting infrastructure can be found in the [bike-rides-infra](https://github.com/tomcant/bike-rides-infra) repository and the following diagram gives a rough overview of the layout within AWS.

![image](https://github.com/user-attachments/assets/359930f7-5422-4e91-b234-7c56a344a254)
