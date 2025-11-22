package db

import (
	"database/sql"
	"fmt"
	"os"
	"time"

	_ "github.com/go-sql-driver/mysql"
	"go.uber.org/zap"
)

func Connect() (*sql.DB, error) {
	dsn := fmt.Sprintf("%s:%s@tcp(%s:%s)/%s?parseTime=true",
		os.Getenv("DB_USER"),
		os.Getenv("DB_PASSWORD"),
		os.Getenv("DB_HOST"),
		os.Getenv("DB_PORT"),
		os.Getenv("DB_NAME"),
	)

	var db *sql.DB
	var err error
	
	maxRetries := 20
	baseDelay := time.Second
	
	logger := zap.L()
	logger.Info("attempting to connect to MySQL", zap.String("host", os.Getenv("DB_HOST")))
	
	for i := 0; i < maxRetries; i++ {
		db, err = sql.Open("mysql", dsn)
		if err != nil {
			waitTime := baseDelay * time.Duration(1<<uint(i))
			if waitTime > 5*time.Second {
				waitTime = 5 * time.Second
			}
			
			logger.Warn("failed to open database connection, retrying...",
				zap.Int("attempt", i+1),
				zap.Int("max_retries", maxRetries),
				zap.Duration("wait_time", waitTime),
				zap.Error(err))
			
			time.Sleep(waitTime)
			continue
		}
		
		err = db.Ping()
		if err == nil {
			logger.Info("successfully connected to MySQL")
			return db, nil
		}
		
		waitTime := baseDelay * time.Duration(1<<uint(i))
		if waitTime > 5*time.Second {
			waitTime = 5 * time.Second
		}
		
		logger.Warn("failed to ping database, retrying...",
			zap.Int("attempt", i+1),
			zap.Int("max_retries", maxRetries),
			zap.Duration("wait_time", waitTime),
			zap.Error(err))
		
		db.Close()
		time.Sleep(waitTime)
	}

	return nil, fmt.Errorf("failed to connect to database after %d attempts: %w", maxRetries, err)
}
