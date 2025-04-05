#!/bin/bash

# Define the list of new providers
providers=(
  "ServiceBindingServiceProvider"
  "RepositoryBindingServiceProvider"
  "ResolverServiceProvider"
  "StrategyFactoryServiceProvider"
  "ValidationTaggingServiceProvider"
  "AuthGuardServiceProvider"
  "MorphMapServiceProvider"
  "AppServiceProvider" # optional, in case you want to regenerate
)

echo "Creating providers..."

# Loop and run make:provider for each
for provider in "${providers[@]}"
do
  php artisan make:provider "$provider"
done

echo "✅ All providers created successfully."
