# 测试说明

## 目录结构

- `Unit/` - 单元测试
- `Integration/` - 集成测试

## Redis测试

Redis测试分为两个部分：

1. **单元测试** - 测试Redis驱动的基本功能，不需要实际的Redis服务器
2. **集成测试** - 测试Redis驱动与实际Redis服务器的交互

### 运行Redis测试

```bash
# 运行所有单元测试
vendor/bin/phpunit --testsuite Unit

# 运行所有集成测试
vendor/bin/phpunit --testsuite Integration

# 只运行Redis相关的测试
vendor/bin/phpunit tests/Unit/Driver/RedisTest.php
vendor/bin/phpunit tests/Integration/Driver/RedisIntegrationTest.php
```

### 测试环境要求

- PHP 8.1+
- Redis扩展
- msgpack扩展
- 可访问的Redis服务器（集成测试需要）

在本地运行集成测试时，需要启动Redis服务器：
```bash
redis-server
```

在GitHub Actions中，Redis服务器会自动启动并配置。