# n8n Docker Setup

This directory contains the Docker setup for n8n workflow automation integration.

## Quick Start

1. **Generate encryption key**:

    ```bash
    node -e "console.log(require('crypto').randomBytes(32).toString('hex'))"
    ```

2. **Update `.env.n8n`** with your credentials

3. **Start n8n**:

    ```bash
    docker-compose -f docker-compose.n8n.yml up -d
    ```

4. **Access n8n**:
    - URL: http://localhost:5678
    - Username: (from .env.n8n)
    - Password: (from .env.n8n)

## Directory Structure

```
storage/n8n/
├── data/          # n8n persistent data and database
├── workflows/     # Exported workflow JSON files
└── logs/          # n8n application logs
```

## Management Commands

### Start n8n

```bash
docker-compose -f docker-compose.n8n.yml up -d
```

### Stop n8n

```bash
docker-compose -f docker-compose.n8n.yml down
```

### View logs

```bash
docker-compose -f docker-compose.n8n.yml logs -f n8n
```

### Restart n8n

```bash
docker-compose -f docker-compose.n8n.yml restart n8n
```

### Check status

```bash
docker-compose -f docker-compose.n8n.yml ps
```

## Workflows

Store your workflow exports in `storage/n8n/workflows/` for version control.

## Backup

To backup n8n data:

```bash
# Backup data directory
tar -czf n8n-backup-$(date +%Y%m%d).tar.gz storage/n8n/data/

# Or copy workflows only
cp storage/n8n/data/*.json storage/n8n/workflows/
```

## Notes

- n8n is accessible only from localhost by default
- Use basic authentication for security
- Keep your encryption key secure and backed up
- Workflows are stored in SQLite database in `data/` directory
