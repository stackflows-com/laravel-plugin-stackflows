# Changelog

All notable changes to `laravel-plugin-stackflows` will be documented in this file.

## 1.0.0 - 202X-XX-XX

- initial release

## 1.1.0 - 2022-01-01

- Configuration file total rewrite
- "BPMN" namespace now became "BusinessProcesses", as "BPMN" does not make sense in our context
- "ExternalTask" naming changed to "ServiceTask" everywhere, because "ServiceTask" is BPMN naming
- "ServiceTaskType" was introduced, it is part of "Camunda" decoupling progress
- Service task input abstraction was decoupled from "Camunda" logic
- "Stackflows" HTTP client was split into two, one is "Stackflows" generic client and another one is for "Camunda" direct communication, later is deprecated as part of decoupling process
- "Stackflows" "Laravel" facade begins to shape into something useful as it will be used for common operations
