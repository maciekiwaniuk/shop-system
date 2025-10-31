#!/usr/bin/env lua

print("Hello, World!")
print("Seed Service is running!")

local timestamp = os.date("%Y-%m-%d %H:%M:%S")
print("Current time: " .. timestamp)

print("Lua version: " .. _VERSION)

-- Simple loop to keep container running
print("Press Ctrl+C to stop...")
while true do
    os.execute("sleep 5")
    local now = os.date("%Y-%m-%d %H:%M:%S")
    print("Still running at: " .. now)
end
